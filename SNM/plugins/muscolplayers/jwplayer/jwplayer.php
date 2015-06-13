<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgMuscolplayers extends JPlugin
{

	function plgMuscolplayers(&$subject, $params)
	{
		parent::__construct($subject, $params);

	}

	static function renderPlayer( $song, $multi = false, $options = array(), $playlist_url = "", $types = array())
	{
		$uri = JFactory::getURI();
		$comp_params =JComponentHelper::getParams( 'com_muscol' );
		
		$document = JFactory::getDocument();
		$db = JFactory::getDBO();

		JHtmlBehavior::framework();
		
		$plugin_root = "plugins/muscolplayers/jwplayer/" ;
		$jwplayer_root = $plugin_root . "jwplayer/" ;
		
		$plugin = JPluginHelper::getPlugin('muscolplayers','jwplayer');
		$params = new JRegistry( $plugin->params );
		
			
			$array_params = array();
			
			//$array_params["controlbar"] = $params->get('controlbar');
			if($params->get('skin')){
				$array_params["skin"] = $uri->base() . $plugin_root . "skins/" . $params->get('skin') .".xml";
			}
			
			$width = $params->get('width', '100%');
			$height = $params->get('height', '30');
			
			$final_array_params = array();
			$string_params = "";
			
			foreach($array_params as $key => $value){
				if($value != "") $final_array_params[] = $key . ": '" . $value ."', " ;
			}
			
			if(!empty($final_array_params)) $string_params =  implode("\n",$final_array_params);
			
			$buttons = array();
			$object = "";
			
			if($multi){

				$unique_id = time();
				
				$jw_playlist = array();

				$videos = false ;

				$k = 0;

				for($i = 0, $n = count($song); $i < $n; $i++){
					if( ($song[$i]->filename != "" || $song[$i]->video != "" )){
						
						if(!isset($song[$i]->image)){
							$query = "SELECT al.image FROM #__muscol_songs AS s LEFT JOIN #__muscol_albums AS al ON al.id = s.album_id WHERE s.id = ". $song[$i]->id ;
							$db->setQuery($query);
							$song[$i]->image = $db->loadResult();

						}
						if(!isset($song[$i]->artist_name)){
							$query = "SELECT ar.artist_name FROM #__muscol_songs AS s LEFT JOIN #__muscol_artists AS ar ON ar.id = s.artist_id WHERE s.id = ". $song[$i]->id ;
							$db->setQuery($query);
							$song[$i]->artist_name = $db->loadResult();

						}

						if(!isset($types[$i])) $types[$i] = "s";

						switch($types[$i] == "v"){
							case 'v':
								if( $song[$i]->video != "" ){

									$video_pieces = explode("?",$song[$i]->video) ;
									if(count($video_pieces) == 2 ){ // http://www.youtube.com/watch?v=6hzrDeceEKc
										$youtube_video_id = str_replace("v=", "", $video_pieces[1]);
									}
									else{ // http://www.youtube.com/v/6hzrDeceEKc OR 6hzrDeceEKc
										$youtube_video_id = str_replace("http://www.youtube.com/v/", "", $song[$i]->video);
									}
									$thefile = "http://www.youtube.com/v/" . $youtube_video_id ;
									
								}

								$jw_playlist[] = "jw_playlist_".$unique_id."[".$k."] = {file: \"".$thefile."\", title:\"".$song[$i]->name."\", description:\"".$song[$i]->artist_name."\", image: \"".$uri->base() . "images/albums/" . $song[$i]->image."\"};" ;

								$k++;

								$videos = true ;
							break;
							default:
								//there is SONG
								if( $song[$i]->filename != ""){

									$thefile = MusColHelper::getSongFileURL($song[$i]) ;
									
									$jw_playlist[] = "jw_playlist_".$unique_id."[".$k."] = {file: \"".$thefile."\", title:\"".$song[$i]->name."\", description:\"".$song[$i]->artist_name."\", image: \"".$uri->base() . "images/albums/" . $song[$i]->image."\"};" ;

									$k++;
									
								}
							break;
						}

						
					}
				}

				$document->addScriptDeclaration( "var jw_playlist_".$unique_id." = new Array(); ".implode(" ",$jw_playlist) );

				if($videos) $videos = "primary: 'flash',";

				$object = "<video id='album_player_".$unique_id."'>".JText::_('LOADING_PLAYER')."</video>
				
							<script type='text/javascript' defer='defer'> 
							var album_player = null;
								jwplayer('album_player_".$unique_id."').setup({
									
									playlist: jw_playlist_".$unique_id.",
									
									height: ".$height.", 
									width: '".$width."',
									autostart: false,
									repeat: 'list',
									".$videos."
									".$string_params."
									listbar: {
								        position: '".$params->get('playlist')."',
								        size: '".$params->get('playlistsize')."'
								      },
									events:{
										onReady: function(event) { album_player = jwplayer('album_player_".$unique_id."'); },
										onPlaylistItem: function(event) { itemListener(event); },
										onPlay: function(event) { first_play(); },
										onPause: function(event) { pause_playlist(event); }
									}
								});
							</script>" ;
				
				
						
			} else {
				
				if( isset($options["force_show_player"]) ){
				
					$unique_id = $song->id;
					
					if(!$song->length) $duration_comment = "//";

					$thefile = MusColHelper::getSongFileURL($song) ;
						
					$object = "<video id='single_player_".$unique_id."'>".JText::_('LOADING_PLAYER')."</video>
				
								<script type='text/javascript' defer='defer'> 
									jwplayer('single_player_".$unique_id."').setup({
										
										file: '".$thefile."', 
										height: ".$height.", 
										width: '".$width."',
										autostart: false,
										".$duration_comment."duration: '".$song->length."', 
										
										events:{
											onPlaylistItem: function(event) { itemListener(event); },
											onPlay: function(event) { first_play(); },
											onPause: function(event) { pause_playlist(); }
										}
									});
								</script>" ;
							
				}
				else{
					
					if($song->filename) $buttons[] = "<!--PLAYBUTTON--><a id='play_button_".$song->position_in_playlist."' class='play_button' href=\"javascript:play_song_position(".$song->position_in_playlist.");\" rel='tooltip' data-original-title='".JText::_('Play')."'><i class='icon-play' id='play_icon_".$song->position_in_playlist."'></i></a><!--/PLAYBUTTON-->";
				
				}
				
				// we'll get the current playlist from the session
				$session =JSession::getInstance('','');
				$playlist_id = $session->get('current_playlist') ; // the playlist is an array
				
				if(!$playlist_id) $playlist_id = 0;
				
				if($params->get('show_add_to_playlist')) { if($song->filename) $buttons[] = "<a class='' href=\"javascript:add_song_to_playlist(".$song->id.",".$playlist_id.",'s');\" rel='tooltip' data-original-title='".JText::_('ADD_TO_CURRENT_PLAYLIST')."'><i class='icon-plus-sign'></i></a>";}
				
				if($song->video != ""){
					
					if($params->get('show_add_to_playlist')) $buttons[] = "<a class='' href=\"javascript:add_song_to_playlist(".$song->id.",".$playlist_id.",'v');\" rel='tooltip' data-original-title='".JText::_('ADD_VIDEO_TO_CURRENT_PLAYLIST')."'><i class='icon-facetime-video'></i></a>";
					
				}

				$object .=  implode(" ",$buttons)  ;
										
			}
			
			$document->addScript($jwplayer_root.'jwplayer.js');
			$document->addScript($jwplayer_root.'playlist_functions.js');
			
			return $object;
			
	}
}
