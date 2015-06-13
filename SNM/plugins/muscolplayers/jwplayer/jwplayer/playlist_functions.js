// JavaScript Document

var muscol_player_module_loaded = false ; 
var muscol_player_plugin_loaded = true;
var played_before = false ; 
var played_beforeM = false ; 

function add_song_to_playlist(song_id,playlist_id,type)
{

	var url = base+extrabase+"/index.php?option=com_muscol&task=add_song_to_playlist&id=" + playlist_id + "&song_id=" + song_id + "&type=" + type ;

	jQuery.ajax({
		url: url,
		success: function(response, textStatus, jqXHR){
			if(muscol_player_module_loaded){
			  //if the module player exists, we refresh it
			  new_items = true;
			  popup_new_items = true;

			  thenewitem = JSON.parse(response);
			  console.log(thenewitem);
			  jw_playlist_module_jwplayer.push(thenewitem) ;

			  if(popup_active && (!popWin)){ // if the popup is active but we havent reloaded it yet
			  	initialize_popup_variables(popup_url);
			  	popWin.jw_playlist_module_jwplayer.push(thenewitem) ;
			  }
			  if ((popWin) && (! popWin.closed)) {
			  	popWin.new_items = true; 
			  	popWin.jw_playlist_module_jwplayer.push(thenewitem) ;
			  }
			  
			  reload_if_possible();

			}
		}
	});

}

function reload_playlist(){
	new_items = false;
	popup_new_items = false;

	if ((popWin)&&(! popWin.closed)) { // we update the playlist on the popup player. SUPER COOL
		popWin.new_items = false;
		popWin.module_jwplayer.load(jw_playlist_module_jwplayer);
	}
	
	else{
		
		if(empty_playlist){
			initPlayer();
			empty_playlist = false ;
			
		}
		
		jwplayer('jwplayer_wrapper').load(jw_playlist_module_jwplayer);
	
	}
	
}

function reload_if_possible(){

	if ((popWin)&&(! popWin.closed)) {
		popWin.reload_if_possible();
	}
	else{

		reload_playlist();  

	}

}

var currentItem = -1; 

function itemListener(obj) {
	
	if(currentItem == -1 && obj.index == 0){}
		else if (obj.index != currentItem) {
		//previousItem = currentItem;
		currentItem = obj.index;
		played_before = true;
		add_song_play_count(songs_position_id[currentItem]) ;
		//alert(currentItem);
	}
	
	//new in 2.4
	highlight_item(obj.index);	
}

//new in 2.4
function highlight_item(position){

	jQuery('.tr_song_link').removeClass('info');

}

//new in 2.4
function pause_playlist(obj){
	//console.log(obj);
	pause_button(currentItem);	
}

function pause_button(position){ //on pause. displays PLAY button

	jQuery('.icon-volume-up').removeClass('icon-volume-up').addClass('icon-play');
	
	jQuery('.play_button').attr('data-original-title','Play');
	
	jQuery('#play_icon_'+position).removeClass('icon-volume-up');
	jQuery('#play_icon_'+position).addClass('icon-play');
	
	jQuery('#play_button_'+position).attr('data-original-title','Play');
	
	//the image on the album cover
	jQuery('#play_button_album').removeClass('pause-lg');
	
}

function play_button(position){ //on play. displays PAUSE button
	
	jQuery('.icon-volume-up').removeClass('icon-volume-up').addClass('icon-play');
	
	jQuery('.play_button').attr('data-original-title','Play');
	
	jQuery('#play_icon_'+position).removeClass('icon-play');
	jQuery('#play_icon_'+position).addClass('icon-volume-up');
	
	jQuery('#play_button_'+position).attr('data-original-title','Pause');
	
	//the image on the album cover
	jQuery('#play_button_album').addClass('pause-lg');
}

function toggle_player(){
	if (typeof video !== 'undefined'){//html5
		if(video.paused){ 
			video.play();
			play_button(currentpos) ;
		}
		else { 
			video.pause();
			pause_button(currentpos) ;
		}
	}else{//flash jw player
		album_player.play();	
	}
}


function first_play(){
	if(!played_before){
		played_before = true;
		if(currentItem == -1){
			currentItem = 0 ;
			add_song_play_count(songs_position_id[0]) ;
		}
	}
	
	//new in 2.4
	//restore_href();
	play_button(currentItem) ;
}	

function add_song_play_count(song_id){
	
	var url = base+extrabase+"/index.php?option=com_muscol&task=add_song_play_count&id="+song_id;
	
	jQuery.ajax({
		url: url
	});
	
}

function play_song_position(position){
	//new in 2.4
	
	if(currentItem == position) album_player.play();
	else album_player.playlistItem(position);
}

var currentItemM = -1; 

function itemListenerModule(obj) {
	
	if(currentItemM == -1 && obj.index == 0){}
		else if (obj.index != currentItemM) {
		//previousItem = currentItem;
		currentItemM = obj.index;
		played_beforeM = true;
		add_song_play_count_module(currentItemM) ;
		//alert(currentItemM);
	}
}  

function add_song_play_count_module(position){

	var url = base+extrabase+"/index.php?option=com_muscol&task=add_song_play_count_module&pos="+position;

	jQuery.ajax({
		url: url
	});
	
}