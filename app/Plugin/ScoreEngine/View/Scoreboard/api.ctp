<?php
echo json_encode([
	'round' => $round,
	'content' => $this->EngineOutputter->generateScoreBoard(),
]);