<?php
namespace Auth\Model;

class RegistrationEntity
{
    protected $username;
    protected $password;
    protected $first_name;
    protected $last_name;


    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->username     = (isset($data['username'])) ? $data['username'] : null;
        $this->password     = (isset($data['password'])) ? $data['password'] : null;
        $this->first_name    = (isset($data['surname'])) ? $data['surname'] : null;
        $this->last_name     = (isset($data['name'])) ? $data['name'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Gets the value of username.
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Sets the value of username.
     *
     * @param mixed $username the username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets the value of password.
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Sets the value of password.
     *
     * @param mixed $password the password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets the value of first_name.
     *
     * @return mixed
     */
    public function getfirst_name()
    {
        return $this->first_name;
    }
    
    /**
     * Sets the value of first_name.
     *
     * @param mixed $first_name the first name
     *
     * @return self
     */
    public function setfirst_name($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Gets the value of last_name.
     *
     * @return mixed
     */
    public function getlast_name()
    {
        return $this->last_name;
    }
    
    /**
     * Sets the value of last_name.
     *
     * @param mixed $last_name the last name
     *
     * @return self
     */
    public function setlast_name($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }
}
