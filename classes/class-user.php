<?php

    class User{

        

        private $mail;
        private $password;
        private $businessName;
        private $currency;

        public function __construct($mail,$password,$businessName,$currency)
        {
            $this->mail = $mail;
            $this->password = $password;
            $this->businessName = $businessName;  
            $this->currency = $currency;
        }

        /**
         * Get the value of mail
         */ 
        public function getMail()
        {
                return $this->mail;
        }

        /**
         * Set the value of mail
         *
         * @return  self
         */ 
        public function setMail($mail)
        {
                $this->mail = $mail;

                return $this;
        }

        /**
         * Get the value of password
         */ 
        public function getPassword()
        {
                return $this->password;
        }

        /**
         * Set the value of password
         *
         * @return  self
         */ 
        public function setPassword($password)
        {
                $this->password = $password;

                return $this;
        }

        /**
         * Get the value of businessName
         */ 
        public function getBusinessName()
        {
                return $this->businessName;
        }

        /**
         * Set the value of businessName
         *
         * @return  self
         */ 
        public function setBusinessName($businessName)
        {
                $this->businessName = $businessName;

                return $this;
        }

        /**
         * Get the value of currency
         */ 
        public function getCurrency()
        {
                return $this->currency;
        }

        /**
         * Set the value of currency
         *
         * @return  self
         */ 
        public function setCurrency($currency)
        {
                $this->currency = $currency;

                return $this;
        }
    }

?>