<?php

require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/resource/lib.php');
require_once($CFG->dirroot . '/repository/upload/lib.php');

class custom_upload_file_to_course extends external_api
{

    /**
     * Returns description of upload parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function upload_file_to_course_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'filename' => new external_value(PARAM_FILE, 'file name', VALUE_OPTIONAL, ""),
            )
        );
    }

    /**
     * Returns description of upload returns
     *
     * @return external_value
     * @since Moodle 2.2
     */
    public static function upload_file_to_course_returns()
    {
        return new external_value(PARAM_TEXT, 'Status message');
    }

    public static function upload_file_to_course($courseid, $filename = "")
    {
        global $DB;

        $random_itemid = rand(100000000, 999999999);
        $file = $DB->get_record('files', array('itemid' => $random_itemid), '*');

        // If random item_id already exists, create a new one
        while ($file) {
            $random_itemid = rand(100000000, 999999999);
            $file = $DB->get_record('files', array('itemid' => $random_itemid), '*');
        }

        $resp = (new repository_upload(4))->process_upload($filename, -1, $types = '*', $savepath = '/', $random_itemid);
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $newcm = self::coursemodule_helper($course);

        $transaction = $DB->start_delegated_transaction();
        $coursemodule_id = add_course_module($newcm);

        $data = self::resource_helper($filename, $courseid, $random_itemid, $coursemodule_id);
        $returnfromfunc = resource_add_instance($data, NULL);

        $DB->set_field('course_modules', 'instance', $returnfromfunc, array('id' => $coursemodule_id));

        $modcontext = context_module::instance($coursemodule_id);
        $sectionid = course_add_cm_to_section($course, $coursemodule_id, 0);

        $transaction->allow_commit();

        return 'Successfully uploaded file to course';
    }

    //
    // Helper -----------------------------------------------------
    //
    /**
     * Helper to create a resource instance
     *
     * @param $filename
     * @param $courseid
     * @param $random_itemid
     * @param $coursemodule_id
     * @return stdClass
     */
    private static function resource_helper($filename, $courseid, $random_itemid, $coursemodule_id)
    {
        $data = new stdClass();
        $data->name = $filename;
        $data->showdescription = 0;
        $data->files = $random_itemid;
        $data->display = 0;
        $data->popupwidth = 620;
        $data->popupheight = 450;
        $data->printintro = 1;
        $data->filterfiles = 0;
        $data->visible = 1;
        $data->visibleoncoursepage = 1;
        $data->cmidnumber = $coursemodule_id;
        $data->availabilityconditionsjson = json_encode(["op" => "&", "c" => [], "showc" => []]);
        $data->completionunlocked = 1;
        $data->completion = 1;
        $data->completionexpected = 0;
        $data->tags = [];
        $data->course = $courseid;
        $data->coursemodule = $coursemodule_id;
        $data->section = 0;
        $data->module = 17;
        $data->modulename = 'resource';
        $data->instance = "";
        $data->add = 'resource';
        $data->update = 0;
        $data->return = 0;
        $data->sr = 0;
        $data->competencies = [];
        $data->competency_rule = 0;
        $data->submitbutton2 = 'Save and return to course';
        $data->revision = 1;
        $data->groupingid = 0;
        $data->completionview = 0;
        $data->completiongradeitemnumber = NULL;
        $data->conditiongradegroup = 0;
        $data->conditionfieldgroup = 0;
        $data->groupmode = 0;
        $data->intro = 0;
        $data->introformat = 1;
        $data->timemodified = time();
        $data->displayoptions = json_encode(['printintro', 1]);
        return $data;
    }

    /**
     * Helper to create a coursemodule instance
     *
     * @param $course
     * @return stdClass
     */
    private static function coursemodule_helper($course)
    {
        $newcm = new stdClass();
        $newcm->course = $course->id;
        $newcm->module = 17; // 17 == resource
        $newcm->visible = 1;
        $newcm->visibleold = 1;
        $newcm->visibleoncoursepage = 1;
        $newcm->groupmode = 0;
        $newcm->groupingid = 0;
        $newcm->showdescription = 0;
        return $newcm;
    }

}