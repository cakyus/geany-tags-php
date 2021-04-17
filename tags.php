<?php

/**
 * Generate Geany PHP Tags File
 * Usage: php tags.php
 *        php tags.php <output> <dir>
 *        php tags.php <output> <file..>
 **/

/**
01604687a7f61aab7e841b2d2cfe9872d8856dfc/src/tagmanager/tm_source_file.c:52
**/

define('TAG_PREFIX_NAME', 200);
define('TAG_PREFIX_LINE', 201);
define('TAG_PREFIX_LOCAL', 202);
define('TAG_PREFIX_POS', 203); // Obsolete
define('TAG_PREFIX_TYPE', 204);
define('TAG_PREFIX_ARGLIST', 205);
define('TAG_PREFIX_SCOPE', 206);
define('TAG_PREFIX_VARTYPE', 207);
define('TAG_PREFIX_INHERITS', 208);
define('TAG_PREFIX_TIME', 209);
define('TAG_PREFIX_ACCESS', 210);
define('TAG_PREFIX_IMPL', 211);
define('TAG_PREFIX_LANG', 212);
define('TAG_PREFIX_INACTIVE', 213); // Obsolete
define('TAG_PREFIX_POINTER', 214);

/** TMTagType
01604687a7f61aab7e841b2d2cfe9872d8856dfc/src/tagmanager/tm_parser.h:22
**/

define('TAG_TYPE_UNDEF', 0); // Unknown type
define('TAG_TYPE_CLASS', 1); // Class declaration
define('TAG_TYPE_ENUM', 2); // Enum declaration
define('TAG_TYPE_ENUMERATOR', 4); // Enumerator value
define('TAG_TYPE_FIELD', 8); // Field (Java only)
define('TAG_TYPE_FUNCTION', 16); // Function definition
define('TAG_TYPE_INTERFACE', 32); // Interface (Java only)
define('TAG_TYPE_MEMBER', 64); // Member variable of class/struct
define('TAG_TYPE_METHOD', 128); // Class method (Java only)
define('TAG_TYPE_NAMESPACE', 256); // Namespace declaration
define('TAG_TYPE_PACKAGE', 512); // Package (Java only)
define('TAG_TYPE_PROTOTYPE', 1024); // Function prototype
define('TAG_TYPE_STRUCT', 2048); // Struct declaration
define('TAG_TYPE_TYPEDEF', 4096); // Typedef
define('TAG_TYPE_UNION', 8192); // Union
define('TAG_TYPE_VARIABLE', 16384); // Variable
define('TAG_TYPE_EXTERNVAR', 32768); // Extern or forward declaration
define('TAG_TYPE_MACRO', 65536); //  Macro (without arguments)
define('TAG_TYPE_MACRO_WITH_ARG', 131072); // Parameterized macro
define('TAG_TYPE_FILE', 262144); // File (Pseudo tag) - obsolete
define('TAG_TYPE_OTHER', 524288); // Other (non C/C++/Java tag)
define('TAG_TYPE_MAX', 1048575); // Maximum value of TMTagType

function geany_tag_to_text($geanyTag){

	$text = $geanyTag->name;

	if (is_null($geanyTag->type) == FALSE){
		$text .= chr(TAG_PREFIX_TYPE).$geanyTag->type;
	}

	if (is_null($geanyTag->arglist) == FALSE){
		$text .= chr(TAG_PREFIX_ARGLIST).$geanyTag->arglist;
	}

	if (is_null($geanyTag->vartype) == FALSE){
		$text .= chr(TAG_PREFIX_VARTYPE).$geanyTag->vartype;
	}

	if (is_null($geanyTag->scope) == FALSE){
		$text .= chr(TAG_PREFIX_SCOPE).$geanyTag->scope;
	}

	$text .= chr(TAG_PREFIX_POINTER).'0';
	return $text;
}

function geany_tags_file_create(){
	$tagFile = new \stdClass;
	$tagFile->tags = new \ArrayObject;
	return $tagFile;
}

function geany_tags_file_write($filePath, $tagFile){

	$fileLine = array();

	foreach ($tagFile->tags as $geanyTag){
		$fileLine[] = geany_tag_to_text($geanyTag);
	}

	sort($fileLine);
	array_unshift($fileLine, "# format=tagmanager");

	$fileText = implode("\n", $fileLine);

	file_put_contents($filePath, $fileText);
}

function geany_tags_add($tagFile){

	$geanyTag = new \stdClass;
	$geanyTag->name = NULL;
	$geanyTag->type = NULL;
	$geanyTag->arglist = NULL;
	$geanyTag->vartype = NULL;
	$geanyTag->scope = NULL;

	$tagFile->tags->append($geanyTag);

	return $geanyTag;
}

function geany_write_tags_standard(){

	$tagFile = geany_tags_file_create();
	$tagFilePath = dirname(__FILE__).'/std.php.tags';

	// contants

	$constants = array();
	$constantGroups = get_defined_constants(TRUE);
	unset($constantGroups['user']);

	foreach ($constantGroups as $constantGroup){
		foreach (array_keys($constantGroup) as $constant){
			$constants[] = $constant;
		}
	}

	foreach ($constants as $constant){
		$geanyTag = geany_tags_add($tagFile);
		$geanyTag->name = $constant;
		$geanyTag->type = TAG_TYPE_MACRO;
	}

	// functions

	$functionGroups = get_defined_functions();
	$functions = array_values($functionGroups['internal']);

	foreach ($functions as $function){
		$geanyTag = geany_tags_add($tagFile);
		$geanyTag->name = $function;
		$geanyTag->type = TAG_TYPE_FUNCTION;
	}

	// classes

	$classes = get_declared_classes();

	foreach ($classes as $class){
		$geanyTag = geany_tags_add($tagFile);
		$geanyTag->name = $class;
		$geanyTag->type = TAG_TYPE_CLASS;
	}

	geany_tags_file_write($tagFilePath, $tagFile);
}

if (defined('FCPATH') == FALSE){
	geany_write_tags_standard();
}
