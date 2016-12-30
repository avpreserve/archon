<?php

/**
 * Collection Content importer script
 *
 * This script takes .csv files and associates each row with a specified collection record.
 * A sample csv/excel file is provided in the archon/incoming folder, to show the necessary format.
 * For user defined fields, the label/head is set directly in the script--see lines 260 and following.
 *
 * @package Archon
 * @subpackage AdminUI
 * @author Kyle Fox
 * 
 */
isset($_ARCHON) or die();

$UtilityCode = 'collectioncontentwithlevels_csv';

$_ARCHON->addDatabaseImportUtility(PACKAGE_COLLECTIONS, $UtilityCode, '3.21', array('csv'), true);

if ($_REQUEST['f'] == 'import-' . $UtilityCode) {
    if (!$_ARCHON->Security->verifyPermissions(MODULE_DATABASE, FULL_CONTROL)) {
        die("Permission Denied.");
    }

    @set_time_limit(0);

    ob_implicit_flush();

    $arrFiles = $_ARCHON->getAllIncomingFiles();

    if (!empty($arrFiles)) {
        $arrEADElements = $_ARCHON->getAllEADElements();
        foreach ($arrEADElements as $objEADElement) {
            $arrEADElementMap[$objEADElement->EADTag] = $objEADElement->ID;
        }

        if (!($PartLevelContainerID = $_ARCHON->getLevelContainerIDFromString('Part'))) {
            echo('Part Level/Container ID not found!');
            return;
        }

        if (!($SeriesLevelContainerID = $_ARCHON->getLevelContainerIDFromString('Series'))) {
            echo('Series Level/Container ID not found!');
            return;
        }
        if (!($SubSeriesLevelContainerID = $_ARCHON->getLevelContainerIDFromString('Sub-Series'))) {
            echo('Sub-Series Level/Container ID not found!');
            return;
        }
        if (!($SubSubSeriesLevelContainerID = $_ARCHON->getLevelContainerIDFromString('Sub-subseries'))) {
            echo('Sub-subseries Level/Container ID not found!');
            return;
        }
        if (!($BoxLevelContainerID = $_ARCHON->getLevelContainerIDFromString('Box'))) {
            echo('Box Level/Container ID not found!');
            return;
        }
        if (!($FolderLevelContainerID = $_ARCHON->getLevelContainerIDFromString('Folder'))) {
            echo('Folder Level/Container ID not found!');
            return;
        }
        if (!($ItemLevelContainerID = $_ARCHON->getLevelContainerIDFromString('Item'))) {
            echo('Item Level/Container ID not found!');
            return;
        }

        foreach ($arrFiles as $Filename => $strCSV) {
            echo("Parsing file $Filename...<br><br>\n\n");

            // Remove byte order mark if it exists.
            $strCSV = ltrim($strCSV, "\xEF\xBB\xBF");

            $arrAllData = getCSVFromString($strCSV);
            // ignore first line?
//            echo '<pre>';
//            print_r($arrAllData);
//            exit;

            foreach ($arrAllData as $key => $arrData) {
                if (!empty($arrData) && $key != 0) {
                    $RecordSeriesNumber = trim($arrData[0]);
                    $CollectionID = $_ARCHON->getCollectionIDForNumber($RecordSeriesNumber);

                    if (!$CollectionID) {
                        echo("Collection " . $RecordSeriesNumber . " not found!<br>\n");
                        continue;
                    }

                    $CurrentContentID = 0;

                    unset($objCurrentContent);
                    unset($objCollectionContent);
                    $PartLevelContainerIdentifier = $arrData[1];
                    if ($PartLevelContainerIdentifier) {
                        $TempContentID = $_ARCHON->getCollectionContentIDFromData($CollectionID, $PartLevelContainerID, $PartLevelContainerIdentifier, $CurrentContentID);
                        if (!$TempContentID) {
                            $objCurrentContent = new CollectionContent();
                            $objCurrentContent->CollectionID = $CollectionID;

                            $objCurrentContent->LevelContainerID = $PartLevelContainerID;
                            $objCurrentContent->LevelContainerIdentifier = $PartLevelContainerIdentifier;

                            $objCurrentContent->ParentID = $CurrentContentID;

                            $objCurrentContent->dbStore();
                            $CurrentContentID = $objCurrentContent->ID;
                        } else {
                            $CurrentContentID = $TempContentID;
                        }
                    }
                    $SeriesLevelContainerIdentifier = $arrData[2];
                    if ($SeriesLevelContainerIdentifier) {
                        $TempContentID = $_ARCHON->getCollectionContentIDFromData($CollectionID, $SeriesLevelContainerID, $SeriesLevelContainerIdentifier, $CurrentContentID);
                        if (!$TempContentID) {
                            $objCurrentContent = new CollectionContent();
                            $objCurrentContent->CollectionID = $CollectionID;

                            $objCurrentContent->LevelContainerID = $SeriesLevelContainerID;
                            $objCurrentContent->LevelContainerIdentifier = $SeriesLevelContainerIdentifier;

                            $objCurrentContent->ParentID = $CurrentContentID;

                            $objCurrentContent->dbStore();
                            $CurrentContentID = $objCurrentContent->ID;
                        } else {
                            $CurrentContentID = $TempContentID;
                        }
                    }

                    $SubSeriesLevelContainerIdentifier = $arrData[3];

                    if ($SubSeriesLevelContainerIdentifier) {
                        $TempContentID = $_ARCHON->getCollectionContentIDFromData($CollectionID, $SubSeriesLevelContainerID, $SubSeriesLevelContainerIdentifier, $CurrentContentID);
                        if (!$TempContentID) {
                            $objCurrentContent = new CollectionContent();
                            $objCurrentContent->CollectionID = $CollectionID;

                            $objCurrentContent->LevelContainerID = $SubSeriesLevelContainerID;
                            $objCurrentContent->LevelContainerIdentifier = $SubSeriesLevelContainerIdentifier;

                            $objCurrentContent->ParentID = $CurrentContentID;
                            $objCurrentContent->dbStore();
                            $CurrentContentID = $objCurrentContent->ID;
                        } else {
                            $CurrentContentID = $TempContentID;
                        }
                    }

                    $SubSubSeriesLevelContainerIdentifier = $arrData[4];

                    if ($SubSubSeriesLevelContainerIdentifier) {
                        $TempContentID = $_ARCHON->getCollectionContentIDFromData($CollectionID, $SubSubSeriesLevelContainerID, $SubSubSeriesLevelContainerIdentifier, $CurrentContentID);
                        if (!$TempContentID) {
                            $objCurrentContent = new CollectionContent();
                            $objCurrentContent->CollectionID = $CollectionID;

                            $objCurrentContent->LevelContainerID = $SubSubSeriesLevelContainerID;
                            $objCurrentContent->LevelContainerIdentifier = $SubSubSeriesLevelContainerIdentifier;

                            $objCurrentContent->ParentID = $CurrentContentID;
                            $objCurrentContent->dbStore();
                            $CurrentContentID = $objCurrentContent->ID;
                        } else {
                            $CurrentContentID = $TempContentID;
                        }
                    }

                    $BoxLevelContainerIdentifier = $arrData[5];

                    if ($BoxLevelContainerIdentifier) {
                        $TempContentID = $_ARCHON->getCollectionContentIDFromData($CollectionID, $BoxLevelContainerID, $BoxLevelContainerIdentifier, $CurrentContentID);
                        if (!$TempContentID) {
                            $objCurrentContent = new CollectionContent();
                            $objCurrentContent->CollectionID = $CollectionID;

                            $objCurrentContent->LevelContainerID = $BoxLevelContainerID;
                            $objCurrentContent->LevelContainerIdentifier = $BoxLevelContainerIdentifier;

                            $objCurrentContent->ParentID = $CurrentContentID;

                            $objCurrentContent->dbStore();
                            $CurrentContentID = $objCurrentContent->ID;
                        } else {
                            $CurrentContentID = $TempContentID;
                        }
                    }

                    $FolderLevelContainerIdentifier = $arrData[6];

                    if ($FolderLevelContainerIdentifier) {
                        $TempContentID = $_ARCHON->getCollectionContentIDFromData($CollectionID, $FolderLevelContainerID, $FolderLevelContainerIdentifier, $CurrentContentID);
                        if (!$TempContentID) {
                            $objCurrentContent = new CollectionContent();
                            $objCurrentContent->CollectionID = $CollectionID;

                            $objCurrentContent->LevelContainerID = $FolderLevelContainerID;
                            $objCurrentContent->LevelContainerIdentifier = $FolderLevelContainerIdentifier;

                            $objCurrentContent->ParentID = $CurrentContentID;

                            $objCurrentContent->dbStore();
                            $CurrentContentID = $objCurrentContent->ID;
                        } else {
                            $CurrentContentID = $TempContentID;
                        }
                    }

                    $ItemLevelContainerIdentifier = $arrData[7];

                    if ($ItemLevelContainerIdentifier) {
                        $TempContentID = $_ARCHON->getCollectionContentIDFromData($CollectionID, $ItemLevelContainerID, $ItemLevelContainerIdentifier, $CurrentContentID);
                        if (!$TempContentID) {
                            $objCurrentContent = new CollectionContent();
                            $objCurrentContent->CollectionID = $CollectionID;

                            $objCurrentContent->LevelContainerID = $ItemLevelContainerID;
                            $objCurrentContent->LevelContainerIdentifier = $ItemLevelContainerIdentifier;

                            $objCurrentContent->ParentID = $CurrentContentID;

                            $objCurrentContent->dbStore();
                            $CurrentContentID = $objCurrentContent->ID;
                        } else {
                            $CurrentContentID = $TempContentID;
                        }
                    }

                    $objCollectionContent = $objCurrentContent;

                    if (!$objCollectionContent) {
                        echo("Failed to create new content!<br>\n");
                        continue;
                    }


                    $objCollectionContent->Title = trim($arrData[8]);
                    $objCollectionContent->Date = trim($arrData[9]);
                    $objCollectionContent->Description = str_replace(" ; ", PHP_EOL, trim($arrData[10]));
                    $objCollectionContent->dbStore();
                    if (!$objCollectionContent->ID) {
                        echo("Error importing!<br>\n");
                        continue;
                    }

                    if ($objCollectionContent->ID) {
                        echo("Imported {$objCollectionContent->Title}.<br>\n");
                    }

                    flush();
                }
            }
        }
    }
}
?>