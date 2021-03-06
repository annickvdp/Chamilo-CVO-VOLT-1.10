<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class RolePermissions extends \CourseEntity
{
    /**
     * @return \Entity\Repository\RolePermissionsRepository
     */
     public static function repository(){
        return \Entity\Repository\RolePermissionsRepository::instance();
    }

    /**
     * @return \Entity\RolePermissions
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var integer $role_id
     */
    protected $role_id;

    /**
     * @var string $tool
     */
    protected $tool;

    /**
     * @var string $action
     */
    protected $action;

    /**
     * @var boolean $default_perm
     */
    protected $default_perm;


    /**
     * Set id
     *
     * @param integer $value
     * @return RolePermissions
     */
    public function set_id($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set c_id
     *
     * @param integer $value
     * @return RolePermissions
     */
    public function set_c_id($value)
    {
        $this->c_id = $value;
        return $this;
    }

    /**
     * Get c_id
     *
     * @return integer 
     */
    public function get_c_id()
    {
        return $this->c_id;
    }

    /**
     * Set role_id
     *
     * @param integer $value
     * @return RolePermissions
     */
    public function set_role_id($value)
    {
        $this->role_id = $value;
        return $this;
    }

    /**
     * Get role_id
     *
     * @return integer 
     */
    public function get_role_id()
    {
        return $this->role_id;
    }

    /**
     * Set tool
     *
     * @param string $value
     * @return RolePermissions
     */
    public function set_tool($value)
    {
        $this->tool = $value;
        return $this;
    }

    /**
     * Get tool
     *
     * @return string 
     */
    public function get_tool()
    {
        return $this->tool;
    }

    /**
     * Set action
     *
     * @param string $value
     * @return RolePermissions
     */
    public function set_action($value)
    {
        $this->action = $value;
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function get_action()
    {
        return $this->action;
    }

    /**
     * Set default_perm
     *
     * @param boolean $value
     * @return RolePermissions
     */
    public function set_default_perm($value)
    {
        $this->default_perm = $value;
        return $this;
    }

    /**
     * Get default_perm
     *
     * @return boolean 
     */
    public function get_default_perm()
    {
        return $this->default_perm;
    }
}