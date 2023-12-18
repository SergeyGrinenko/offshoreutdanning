<?php

require_once 'X2_Admin_Api_Client.php';

class X2_TestRestApi
{
    protected $client;

    public function __construct()
    {
        $this->client = X2_Admin_Api_Client::getInstance('wp-offutd ', 'lwiuhgHU788#1jk=');
        $this->client->setEnv(X2_Admin_Api_Client::ENV_PROD);

        $this->getCourseDates();
        $this->getCourseTypes();
        $this->getCourseContexts();
        $this->getCourseLocalities();
    }

    private function getCourseDates()
    {
        $res =
            $this->client
                ->setRoute('course', 'getDates')
                ->setGet([
                    'locality_id' => [5, 11],
                    'coursetype_id' => 71
                ])
                ->execute();

//        var_export($res);
    }

    private function getCourseLocalities()
    {
        $res =
            $this->client
                ->setRoute('course', 'getLocalities')
                ->execute();

//        var_export($res);
    }

    private function getCourseContexts()
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseContexts')
                ->execute();

//        var_export($res);
    }

    public function getCourseTypes()
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseTypes')
                ->execute();


        return $res;
//        var_export($res);
    }


    public function getCourseType($coursetype_id)
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseTypes')
                ->execute();


        return $res[$coursetype_id];
//        var_export($res);
    }


    /* Start Public Functions */

    public function getAllCourses()
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseDates')
                ->setGet()
                ->execute();
        return $res;
    }

    public function getCourseDate($coursetype_id)
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseDates')
                ->setGet([
                    'coursetype_id' => $coursetype_id,
                ])
                ->execute();
        return $res;
    }

    public function getDate($course_id, $coursetype_id)
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseDates')
                ->setGet([
                    'course_id' => $course_id,
                    'coursetype_id' => $coursetype_id,
                    'enrollable' => 'auto',
                ])
                ->execute();
        return $res;
    }

    public function getLocation()
    {
        $res =
            $this->client
                ->setRoute('course', 'getLocalities')
                ->execute();

        return $res;
    }

    public function getContext()
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseContexts')
                ->execute();

        return $res;
    }

    public function getType()
    {
        $res =
            $this->client
                ->setRoute('course', 'getCourseTypes')
                ->execute();

        return $res;
    }

}



