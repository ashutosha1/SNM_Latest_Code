<?php

	//	Path to the location of the fValidate javascript files
	$jspath = "js/";

	//	No modifications necessary past here
	$compressed		= ( isset( $_GET['compressed'] ) )?
						$_GET['compressed']:
						false;
	$compression 	= ( $compressed == true || $compressed == 'yes' ) ? true : false;

	$language			= ( isset( $_GET['language'] ) )?
						$_GET['language']:
						"enUS";
	
	$fvServ			= new fValidateServer( $jspath, $compression, $_GET['modules'] . ",lang-" . $language );
	$fvServ->writeOutput();

	class fValidateServer
	{
		var	$directory		= "";
		var $ouput			= "";
		var $modules		= array( 'core', 'config' );
		var $compression	= false;
		
		function fValidateServer( $dir, $comp, $mods )
		{
			$this->directory	= $dir;
			$this->compression	= $comp;
			$this->addModules( $mods );
		}

		function addModules( $modules )
		{
			if ( $modules != '' )
			{
				$this->modules	= array_merge( $this->modules, explode( "|", $modules ) );
			}
			else
			{
				$this->output .= "alert( 'Message from fValidate.servers.php:\nYou didn't choose any validator modules for inclusion!' );";
			}
		}
		
		function compress()
		{
			$patterns		= array(
				"/([^\/]*)\/\/.*$/m",	// Removes single-line comments
				"/^\s+/m"			// Removes leading spaces/tabs
				);
			$replacements	= array(
				"\\1",
				" "
				);
			$this->output	= preg_replace( $patterns, $replacements, $this->output );
		}

		function generateOutput()
		{
			foreach( $this->modules as $module )
			{
				$path = $this->directory . "fValidate." . $module . ".js";
				if ( file_exists( $path ) )
				{
					$this->output .= "\n" . implode( "", file( $path ) );
				}
			}

			if ( $this->compression == true )
			{
				$this->compress();
			}
		}

		function writeOutput()
		{
			$this->generateOutput();
			echo( $this->output );
		}
	}	// end of class
?>