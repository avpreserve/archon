<?php

/**
 * Description of Session
 *
 * @author Paul Sorensen
 */
class Session extends AObject
{

   /**
    * Returns time current session will expire
    *
    * @return timestamp
    */
   public function getExpiration()
   {
      return $this->Expires;
   }

   /**
    * Returns Remote Host location
    *
    * @return string
    */
   public function getRemoteHost()
   {
      return $this->RemoteHost;
   }

   /**
    * Returns User ID
    *
    * @return integer
    */
   public function getUserID()
   {
      return $this->UserID;
   }


   /**
    * Deletes ArchonSession from the database
    *
    * @return boolean
    */
   public function dbDelete()
   {
      global $_ARCHON;

      if(!$_ARCHON->deleteObject($this, MODULE_SESSIONS, 'tblCore_Sessions'))
      {
         return false;
      }

      return true;
   }





   /**
    * Loads ArchonSession from the database
    *
    * @return boolean
    */
   public function dbLoad()
   {
      global $_ARCHON;

      if(!$_ARCHON->loadObject($this, 'tblCore_Sessions'))
      {
         return false;
      }

      $this->dbLoadUser();

      return true;
   }





   /**
    * Loads User for ArchonSession from the database
    *
    * @return boolean
    */
   public function dbLoadUser()
   {
      global $_ARCHON;

      
      $this->User = New User($this->UserID);

      return ($this->User->dbLoad());
   }

   /**
    * Returns Session object as a formatted string
    *
    * @return string
    */
   public function toString($MakeIntoLink = LINK_NONE)
   {      
      global $_ARCHON;

      if(!$this->ID)
      {
         $_ARCHON->declareError("Could not convert Session to string: Session ID not defined.");
         return false;
      }

      if($MakeIntoLink == LINK_EACH || $MakeIntoLink == LINK_TOTAL)
      {
         if($_ARCHON->QueryStringURL)
         {
            $q = '&amp;q=' . $_ARCHON->QueryStringURL;
         }

         $String .= " <a href='?p=admin/core/sessions&amp;id={$this->ID}{$q}'> ";
      }

      $String .=  $this->getString('RemoteHost');

      if($MakeIntoLink == LINK_EACH || $MakeIntoLink == LINK_TOTAL)
      {
         $String .= '</a>';
      }

      return $String;
   }






   /**
    * User ID
    *
    * @var integer
    */
   public $UserID = 0;

   /**
    * Remote Host for session
    *
    * @var string
    */
   public $RemoteHost = '';

   /**
    * Expiration time (timestamp)
    *
    * @var integer
    */
   public $Expires = 0;

   /**
    * Persistent Session
    *
    * @var integer
    */
   public $Persistent = 0;

   /**
    * Secure Connection
    *
    * @var integer
    */
   public $SecureConnection = 0;

   /**
    * @var User
    */
   public $User = NULL;

}
?>
