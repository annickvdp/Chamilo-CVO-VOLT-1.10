<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class SessionRelCourse extends \Entity
{
    /**
     * @return \Entity\Repository\SessionRelCourseRepository
     */
     public static function repository(){
        return \Entity\Repository\SessionRelCourseRepository::instance();
    }

    /**
     * @return \Entity\SessionRelCourse
     */
     public static function create(){
        return new self();
    }

    /**
     * @var smallint $id_session
     */
    protected $id_session;

    /**
     * @var string $course_code
     */
    protected $course_code;

    /**
     * @var smallint $nbr_users
     */
    protected $nbr_users;


    /**
     * Set id_session
     *
     * @param smallint $value
     * @return SessionRelCourse
     */
    public function set_id_session($value)
    {
        $this->id_session = $value;
        return $this;
    }

    /**
     * Get id_session
     *
     * @return smallint 
     */
    public function get_id_session()
    {
        return $this->id_session;
    }

    /**
     * Set course_code
     *
     * @param string $value
     * @return SessionRelCourse
     */
    public function set_course_code($value)
    {
        $this->course_code = $value;
        return $this;
    }

    /**
     * Get course_code
     *
     * @return string 
     */
    public function get_course_code()
    {
        return $this->course_code;
    }

    /**
     * Set nbr_users
     *
     * @param smallint $value
     * @return SessionRelCourse
     */
    public function set_nbr_users($value)
    {
        $this->nbr_users = $value;
        return $this;
    }

    /**
     * Get nbr_users
     *
     * @return smallint 
     */
    public function get_nbr_users()
    {
        return $this->nbr_users;
    }
}