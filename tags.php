<?php

/**
 * Generate Geany PHP Tags File
 * Usage: php tags.php <output> <files..>
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

class Geany_Tags_Tag {

	public $name;
	public $type;
	public $arglist;
	public $vartype;
	public $scope;

	public function saveText() {
		$text = $this->name;
		if (is_null($this->type) == FALSE){
			$text .= chr(TAG_PREFIX_TYPE).$this->type;
		}
		if (is_null($this->arglist) == FALSE){
			$text .= chr(TAG_PREFIX_ARGLIST).$this->arglist;
		}
		if (is_null($this->vartype) == FALSE){
			$text .= chr(TAG_PREFIX_VARTYPE).$this->vartype;
		}
		if (is_null($this->scope) == FALSE){
			$text .= chr(TAG_PREFIX_SCOPE).$this->scope;
		}
		$text .= chr(TAG_PREFIX_POINTER).'0';
		return $text;
	}
}

class Geany_Tags_File {

	protected $fileHandle;
	protected $writeHeaderCompleted;
	protected $tags;

	public function __construct(){
		$this->writeHeader = FALSE;
	}

	public function createTag($tagName){
		$tag = new Geany_Tags_Tag;
		$tag->name = $tagName;
		return $tag;
	}

	public function writeTag($tag){
		if ($this->writeHeaderCompleted == FALSE){
			fwrite($this->fileHandle, "# format=tagmanager\n");
			$this->writeHeaderCompleted = TRUE;
		}
		fwrite($this->fileHandle, $tag->saveText()."\n");
	}

	public function open($filePath){
		$fileHandle = fopen($filePath, 'w+');
		if ($fileHandle){
			$this->fileHandle = $fileHandle;
		}
	}

	public function close(){
		fclose($this->fileHandle);
	}

}

class Geany_Tags_Controller {

	public function __construct() {}

	public function execute(){

		$tagFile = new Geany_Tags_File;

		$tagFile->open(dirname(__FILE__).'/std.php.tags');
		$constants = array();
		$constantGroups = get_defined_constants(TRUE);

		unset($constantGroups['user']);
		foreach ($constantGroups as $constantGroup){
			foreach (array_keys($constantGroup) as $constant){
				$constants[] = $constant;
			}
		}
		foreach ($constants as $constant){
			$tag = $tagFile->createTag($constant);
			$tag->type = TAG_TYPE_MACRO;
			$tagFile->writeTag($tag);
		}

		$tagFile->close();
	}
}

if (defined('FCPATH') == FALSE){
	$controller = new \Geany_Tags_Controller;
	$controller->execute();
}
