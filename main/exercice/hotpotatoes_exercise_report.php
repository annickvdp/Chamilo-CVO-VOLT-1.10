<?php
/* For licensing terms, see /license.txt */
/**
 *	Exercise list: This script shows the list of exercises for administrators and students.
 *	@package chamilo.exercise
 *	@author hubert.borderiou
 *
 */

use ChamiloSession as Session;

// name of the language file that needs to be included
$language_file = array('exercice');

// including the global library
require_once '../inc/global.inc.php';
require_once '../gradebook/lib/be.inc.php';

// Setting the tabs
$this_section = SECTION_COURSES;

$htmlHeadXtra[] = api_get_jqgrid_js();

// Access control
api_protect_course_script(true, false, true);

// including additional libraries
require_once 'exercise.class.php';
require_once 'exercise.lib.php';
require_once 'question.class.php';
require_once 'answer.class.php';
require_once api_get_path(LIBRARY_PATH).'fileManage.lib.php';
require_once api_get_path(LIBRARY_PATH).'fileUpload.lib.php';
require_once 'hotpotatoes.lib.php';
require_once api_get_path(LIBRARY_PATH).'mail.lib.inc.php';

// need functions of statsutils lib to display previous exercices scores
require_once api_get_path(LIBRARY_PATH) . 'statsUtils.lib.inc.php';

// document path
$documentPath = api_get_path(SYS_COURSE_PATH).$_course['path']."/document";

/*	Constants and variables */
$is_allowedToEdit = api_is_allowed_to_edit(null, true) || api_is_drh();
$is_tutor = api_is_allowed_to_edit(true);

$TBL_QUESTIONS = Database :: get_course_table(TABLE_QUIZ_QUESTION);
$TBL_TRACK_EXERCICES = Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
$TBL_TRACK_HOTPOTATOES_EXERCICES = Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_HOTPOTATOES);
$TBL_LP_ITEM_VIEW = Database :: get_course_table(TABLE_LP_ITEM_VIEW);

$course_id = api_get_course_int_id();
$hotpotatoes_path = isset($_REQUEST['path']) ? $_REQUEST['path'] : null;
$filter_user = isset($_REQUEST['filter_by_user']) ? intval($_REQUEST['filter_by_user']) : null;

if (empty($hotpotatoes_path)) {
    api_not_allowed();
}

if (!$is_allowedToEdit) {
   // api_not_allowed();
}

if (!empty($_REQUEST['path'])) {
    $parameters['path'] = Security::remove_XSS($_REQUEST['path']);
}

$origin = isset($origin) ? $origin : null;

if (!empty($_REQUEST['export_report']) && $_REQUEST['export_report'] == '1') {
    if (api_is_platform_admin() || api_is_course_admin() || api_is_course_tutor() || api_is_course_coach()) {
        $load_extra_data = false;
        if (isset($_REQUEST['extra_data']) && $_REQUEST['extra_data'] == 1) {
            $load_extra_data = true;
        }

        require_once 'hotpotatoes_exercise_result.class.php';
        $export = new HotpotatoesExerciseResult();
        $export->exportCompleteReportCSV($documentPath, $hotpotatoes_path);
        exit;
    } else {
        api_not_allowed(true);
    }
}
$actions = null;
if ($is_allowedToEdit && $origin != 'learnpath') {
    // the form
    if (api_is_platform_admin() || api_is_course_admin() || api_is_course_tutor() || api_is_course_coach()) {
        $actions .= '<a id="export_opener" href="'.api_get_self().'?export_report=1&path='.Security::remove_XSS($hotpotatoes_path).' ">'.Display::return_icon('save.png',   get_lang('Export'),'',ICON_SIZE_MEDIUM).'</a>';
    }
} else {
    $actions .= '<a href="exercice.php">' . Display :: return_icon('back.png', get_lang('GoBackToQuestionList'),'',ICON_SIZE_MEDIUM).'</a>';
}

if ($is_allowedToEdit) {
    $action = isset($_GET['action']) ? $_GET['action'] : null;
    switch ($action) {
        case 'delete':
            $fileToDelete = isset($_GET['id']) ? $_GET['id'] : null;
            deleteAttempt($fileToDelete);
            Session::write('message', Display::return_message(get_lang('ItemDeleted')));
            $url = api_get_self().'?'.api_get_cidreq().'&path='.$hotpotatoes_path;
            header("Location: $url");
            exit;
            break;

    }
}

$nameTools = get_lang('Results');

if ($is_allowedToEdit || $is_tutor) {
    $nameTools = get_lang('StudentScore');
    $interbreadcrumb[] = array("url" => "exercice.php","name" => get_lang('Exercices'));
    $objExerciseTmp = new Exercise();
    /*if ($objExerciseTmp->read($exercise_id)) {
        $interbreadcrumb[] = array("url" => "admin.php?exerciseId=".$exercise_id, "name" => $objExerciseTmp->name);
    }*/
} else {
    $interbreadcrumb[] = array("url" => "exercice.php","name" => get_lang('Exercices'));
    $objExerciseTmp = new Exercise();
    /*if ($objExerciseTmp->read($exercise_id)) {
        $nameTools = get_lang('Results').': '.$objExerciseTmp->name;
    }*/
}

Display :: display_header($nameTools);

$actions = Display::div($actions, array('class'=> 'actions'));

$extra =  '<script type="text/javascript">
    $(document).ready(function() {

        $( "#dialog:ui-dialog" ).dialog( "destroy" );

        $( "#dialog-confirm" ).dialog({
                autoOpen: false,
                show: "blind",
                resizable: false,
                height:300,
                modal: true
         });

        $("#export_opener").click(function() {
            var targetUrl = $(this).attr("href");
            $( "#dialog-confirm" ).dialog({
                width:400,
                height:300,
                buttons: {
                    "'.addslashes(get_lang('Download')).'": function() {
                        var export_format = $("input[name=export_format]:checked").val();
                        var extra_data  = $("input[name=load_extra_data]:checked").val();
                        location.href = targetUrl+"&export_format="+export_format+"&extra_data="+extra_data;
                        $( this ).dialog( "close" );
                    },
                }
            });
            $( "#dialog-confirm" ).dialog("open");
            return false;
        });
    });
    </script>';

$extra .= '<div id="dialog-confirm" title="'.get_lang("ConfirmYourChoice").'">';
$form = new FormValidator('report', 'post', null, null, array('class' => 'form-vertical'));
$form->addElement('radio', 'export_format', null, get_lang('ExportAsCSV'), 'csv', array('id' => 'export_format_csv_label'));
//$form->addElement('radio', 'export_format', null, get_lang('ExportAsXLS'), 'xls', array('id' => 'export_format_xls_label'));
//$form->addElement('checkbox', 'load_extra_data', null, get_lang('LoadExtraData'), '0', array('id' => 'export_format_xls_label'));
$form->setDefaults(array('export_format' => 'csv'));
$extra .= $form->return_form();
$extra .= '</div>';

if ($is_allowedToEdit) {
    echo $extra;
}

echo $actions;

$url = api_get_path(WEB_AJAX_PATH).'model.ajax.php?a=get_hotpotatoes_exercise_results&path='.$hotpotatoes_path.'&filter_by_user='.$filter_user;
$action_links = '';

// Generating group list

$group_list = GroupManager::get_group_list();
$group_parameters = array('group_all:'.get_lang('All'),'group_none:'.get_lang('None'));

foreach ($group_list as $group) {
    $group_parameters[] = $group['id'].':'.$group['name'];
}
if (!empty($group_parameters)) {
    $group_parameters = implode(';', $group_parameters);
}

if ($is_allowedToEdit || $is_tutor) {
    // The order is important you need to check the the $column variable in the model.ajax.php file
    $columns = array(
        get_lang('FirstName'),
        get_lang('LastName'),
        get_lang('LoginName'),
        get_lang('Group'),
        get_lang('StartDate'),
        get_lang('Score'),
        get_lang('Actions')
    );

  // Column config
  // @todo fix search firstname/lastname that doesn't work. rmove search for the moment
	$column_model   = array(
        array('name'=>'firstname',      'index'=>'firstname',		'width'=>'50',   'align'=>'left', 'search' => 'false'),
        array('name'=>'lastname',		    'index'=>'lastname',		'width'=>'50',   'align'=>'left', 'formatter'=>'action_formatter', 'search' => 'false'),
        array('name'=>'login',          'hidden'=>'true',       'index'=>'username',        'width'=>'40',   'align'=>'left', 'search' => 'false'),
        array('name'=>'group_name',		  'index'=>'group_id',    'width'=>'40',   'align'=>'left', 'search' => 'false'),
        array('name'=>'exe_date',		    'index'=>'exe_date',		'width'=>'60',   'align'=>'left', 'search' => 'false'),
        array('name'=>'score',			    'index'=>'exe_result',	'width'=>'50',   'align'=>'left', 'search' => 'false'),
        array('name'=>'actions',        'index'=>'actions',     'width'=>'60',  'align'=>'left', 'search' => 'false')
    );

    $action_links = '
    // add username as title in lastname filed - ref 4226
    function action_formatter(cellvalue, options, rowObject) {
        // rowObject is firstname,lastname,login,... get the third word
        var loginx = "'.api_htmlentities(sprintf(get_lang("LoginX"), ":::"), ENT_QUOTES).'";
        var tabLoginx = loginx.split(/:::/);
        // tabLoginx[0] is before and tabLoginx[1] is after :::
        // may be empty string but is defined
        return "<span title=\""+tabLoginx[0]+rowObject[2]+tabLoginx[1]+"\">"+cellvalue+"</span>";
    }';
} else {
    //The order is important you need to check the the $column variable in the model.ajax.php file
    $columns = array(
        get_lang('StartDate'),
        get_lang('Score'),
        get_lang('Actions')
    );

    //Column config
    // @todo fix search firstname/lastname that doesn't work. rmove search for the moment
    $column_model  = array(
        array('name'=>'exe_date',		    'index'=>'exe_date',		'width'=>'60',   'align'=>'left', 'search' => 'false'),
        array('name'=>'score',			    'index'=>'exe_result',	'width'=>'50',   'align'=>'left', 'search' => 'false'),
        array('name'=>'actions',        'index'=>'actions',     'width'=>'60',  'align'=>'left', 'search' => 'false')
    );
}

//Autowidth
$extra_params['autowidth'] = 'true';

//height auto
$extra_params['height'] = 'auto';
?>
<script>
function setSearchSelect(columnName) {
    $("#results").jqGrid('setColProp', columnName,
    {
       searchoptions:{
            dataInit:function(el){
                $("option[value='1']",el).attr("selected", "selected");
                setTimeout(function(){
                    $(el).trigger('change');
                },1000);
            }
        }
    });
}

function exportExcel() {
    var mya=new Array();
    mya=$("#results").getDataIDs();  // Get All IDs
    var data=$("#results").getRowData(mya[0]);     // Get First row to get the labels
    var colNames=new Array();
    var ii=0;
    for (var i in data){colNames[ii++]=i;}    // capture col names
    var html="";

    for(i=0;i<mya.length;i++) {
        data=$("#results").getRowData(mya[i]); // get each row
        for(j=0;j<colNames.length;j++) {
            html=html+data[colNames[j]]+","; // output each column as tab delimited
        }
        html=html+"\n";  // output each row with end of line
    }
    html = html+"\n";  // end of line at the end

    var form = $("#export_report_form");

    $("#csvBuffer").attr('value', html);
    form.target='_blank';
    form.submit();
}

$(function() {
<?php
    echo Display::grid_js('results', $url, $columns, $column_model, $extra_params, array(), $action_links, true);

    if ($is_allowedToEdit || $is_tutor) { ?>

        //setSearchSelect("status");
        //
        //view:true, del:false, add:false, edit:false, excel:true}
        $("#results").jqGrid('navGrid','#results_pager', {view:true, edit:false, add:false, del:false, excel:false},
            {height:280, reloadAfterSubmit:false}, // view options
            {height:280, reloadAfterSubmit:false}, // edit options
            {height:280, reloadAfterSubmit:false}, // add options
            {reloadAfterSubmit: false}, // del options
            {width:500} // search options
        );

        //Adding search options
        var options = {
            'stringResult': true,
            'autosearch' : true,
            'searchOnEnter':false
        }
        jQuery("#results").jqGrid('filterToolbar',options);
        var sgrid = $("#results")[0];
        sgrid.triggerToolbar();

    <?php } ?>
});
</script>
<form id="export_report_form" method="post" action="hotpotatoes_exercise_report.php">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
    <input type="hidden" name="export_report" id="export_report" value="1" />
    <input type="hidden" name="path" id="path" value="<?php echo $hotpotatoes_path ?>" />
</form>
<?php

$showMessage = Session::read('message');
Session::erase('message');
echo isset($showMessage) ? $showMessage : null;
echo Display::grid_html('results');
Display :: display_footer();
