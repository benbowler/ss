<div class="pre-content"></div>
<div class="quiz quiz_<?php echo $_SESSION["wpsqt"][$quizName]["sections"][$sectionKey]["item_id"]; ?> section_<?php  echo $sectionKey; ?>">
<h1><?php echo $_SESSION["wpsqt"][$quizName]["sections"][$sectionKey]["name"]; ?></h1>

<form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>">
	<input type="hidden" name="step" value="<?php echo ($_SESSION["wpsqt"]["current_step"]+1); ?>">
	<input type="hidden" name="wpsqt_nonce" value="<?php echo WPSQT_NONCE_CURRENT; ?>" />
<?php 		
$answers = ( isset($_SESSION["wpsqt"][$quizName]["sections"][$sectionKey]["answers"]) ) ? $_SESSION["wpsqt"][$quizName]["sections"][$sectionKey]["answers"] : array();
foreach ($_SESSION["wpsqt"][$quizName]["sections"][$sectionKey]["questions"] as $questionKey => $question) { ?>
	
	<div class="wpst_question wpst_question_<?php echo $question['id']; // Ben: include question id  ?>">
		<p class="wpst_question_text">
		<?php
			$questionId = $question['id'];		
			$givenAnswer = isset($answers[$questionId]['given']) ? $answers[$questionId]['given'] : array();
			
			if ( isset($question["required"]) &&  $question["required"] == "yes" ){ 
				?>
				<font color="#FF0000"><strong>*
				
			<?php			
				// See if the question has been missed and this a replay if not end the red text here.
				if ( empty($_SESSION['wpsqt']['current_message']) || in_array($questionId,$_SESSION['wpsqt']['required']) ){
					?></strong></font><?php 
				}
			}

			echo stripslashes($question["name"]);

			// See if the question has been missed and this is a replay
			if ( !empty($_SESSION['wpsqt']['current_message']) && !in_array($questionId,$_SESSION['wpsqt']['required']) ){
				?></strong></font><?php 
			}	
			?>
		</p>
			
			<?php if ( isset($question['image']) ){ ?>
			<p><?php echo stripslashes($question['image']); ?></p>
			<?php } ?>
			
		<?php require Wpsqt_Question::getDisplayView($question); ?>
	</div>		
<?php } ?>

<?php
if ($sectionKey == (count($_SESSION['wpsqt'][$quizName]['sections']) - 1)) {
	?><p><input type='submit' value='Submit' class='button-secondary' /></p><?php
} else {
	?><p><input type='submit' value='Next &raquo;' class='button-secondary' /></p><?php
}
?>
</form>
</div>
<div class="post-content"></div>