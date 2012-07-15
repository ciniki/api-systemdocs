<?php
//
// Description
// -----------
// This method returns a list of functions which are missing argument descriptions.
// 
// Arguments
// ---------
// api_key:
// auth_token:
// package:			(optional) The package to get the errors from.
//
// Returns
// -------
// <functions>
//	<function id="34" package="ciniki" module="artcatalog" file="get" />
// </functions>
//
function ciniki_systemdocs_toolsNoArgDesc($ciniki) {

    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'package'=>array('required'=>'no', 'blank'=>'no', 'errmsg'=>'No package specified'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];	

	//
	// Make suee this module is activated, and
	// check permission to run this function for this business
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'systemdocs', 'private', 'checkAccess');
	$rc = ciniki_systemdocs_checkAccess($ciniki, 'ciniki.systemdocs.toolsImproperCheckAccess');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	$strsql = "SELECT ciniki_systemdocs_api_functions.id, "
		. "ciniki_systemdocs_api_functions.package, ciniki_systemdocs_api_functions.module, "
		. "ciniki_systemdocs_api_functions.type, ciniki_systemdocs_api_functions.file "
		. "FROM ciniki_systemdocs_api_functions, ciniki_systemdocs_api_function_args "
		. "WHERE ciniki_systemdocs_api_functions.id = ciniki_systemdocs_api_function_args.function_id "
		. "AND ciniki_systemdocs_api_function_args.description = '' "
		. "";
	if( isset($args['package']) && $args['package'] != '' ) {
		$strsql .= "AND ciniki_systemdocs_api_functions.package = '" . ciniki_core_dbQuote($ciniki, $args['package']) . "' ";
	}
	$strsql .= "ORDER BY ciniki_systemdocs_api_functions.package, "
		. "ciniki_systemdocs_api_functions.module, ciniki_systemdocs_api_functions.file "
		. "";
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'systemdocs', array(
		array('container'=>'functions', 'fname'=>'id', 'name'=>'function', 
			'fields'=>array('id', 'package', 'module', 'type', 'file')),
		));
	if( $rc['stat'] != 'ok' ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'642', 'msg'=>'Unable to find any functions', 'err'=>$rc['err']));
	}
	if( !isset($rc['functions']) ) {	
		return array('stat'=>'ok', 'functions'=>array());
	}

	return array('stat'=>'ok', 'functions'=>$rc['functions']);
}
?>
