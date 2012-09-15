<?php 
$currentPoints = 0; 
$totalPoints = 0;
$hardPoints = 0;

foreach ( $_SESSION['wpsqt'][$quizName]['sections'] as $section ){ ?>
		<h3><?php echo $section['name']; ?></h3>
		
		<?php
			if (!isset($section['questions'])){
				echo 'Error - no questions<br />Quitting.';
				exit;
			}
				foreach ($section['questions'] as $questionKey => $questionArray){ 
					$questionId = $questionArray['id'];
				?>
					
				<h4><?php print stripslashes($questionArray['name']); ?></h4>
				<?php if ( ucfirst($questionArray['type']) == 'Multiple' 
						|| ucfirst($questionArray['type']) == 'Single'  ){
						if ( isset($section['answers'][$questionId]['mark']) 
						  && $section['answers'][$questionId]['mark'] == 'correct' ){
							$currentPoints++;
							$hardPoints++;
						}
						$totalPoints++;	
					?>				
					<b><u>Mark</u></b> - <?php if (isset($section['answers'][$questionId]['mark'])) { echo $section['answers'][$questionId]['mark']; } else { echo 'Incorrect'; } ?><br />
					<b><u>Answers</u></b>
					<p class="answer_given">
						<ol>
							<?php foreach ($questionArray['answers'] as $answerKey => $answer){ ?>
								  <li><font color="<?php echo ( $answer['correct'] != 'yes' ) ?  (isset($section['answers'][$questionId]['given']) &&  in_array($answerKey, $section['answers'][$questionId]['given']) ) ? '#FF0000' :  '#000000' : '#00FF00' ; ?>"><?php echo esc_html(stripslashes($answer['text'])); ?></font><?php if (isset($section['answers'][$questionId]['given']) && in_array($answerKey, $section['answers'][$questionId]['given']) ){ ?> - Given<?php }?></li>
							<?php } ?>
						</ol>
					</p>
					<?php if (isset($questionArray['explanation'])) { ?>
						<p class="wpsqt-answer-explanation"><?=$questionArray['explanation']?></p>
					<?php } ?>
				<?php } else { 
					?>				
					<b><u>Answer Given</u></b>
					<p class="answer_given" style="background-color : #c0c0c0; border : 1px dashed black; padding : 5px;overflow:auto;height : 200px;"><?php if ( isset($section['answers'][$questionId]['given']) && is_array($section['answers'][$questionId]['given']) ){ echo nl2br(esc_html(stripslashes(current($section['answers'][$questionId]['given'])))); } ?></p>
					<?php if ( isset($questionArray['hint']) && $questionArray['hint'] != "" ) { ?>- <a href="#" class="show_hide_hint">Show/Hide Hint</a></p>
					<div class="hint">
						<h5>Hint</h5>
						<p style="background-color : #c9c9c9;padding : 5px;"><?php echo nl2br(esc_html(stripslashes($questionArray['hint']))); ?></p>
					</div>
					<?php } else { ?></p><?php } ?>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	<p><font size="+3">Total Points <?php echo $_SESSION['wpsqt']['current_score']; ?></font></p>
