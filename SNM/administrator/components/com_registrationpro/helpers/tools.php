<?php

function getUniqFck() {
	return "?fck=".rand(0, 10000);
}

function getImageName($id, $user_id) {
	return "event_".$id.".jpg";
}

?>