<?php
class WpHelper{

	
	
	/**
	    $optiongroup=name of the optionsgroup you want to generate a form, for. If isarray optiongroup is the name of the option with the array.
		$options=array('Label'=>'key');
		if $optionNames is not supplied the function will retrieve the options it self and use the optionkey as label.
	 */
	static function tabbedOptionsForm($optiongroup,$tabs,$saveAllTabs=true){		
/*		if(!$options){
			$options=array();
			global $new_whitelist_options;
			$options=$new_whitelist_options[$optiongroup];
		}*/
		$values=get_option($optiongroup);
		?>
		<?php if($saveAllTabs):?>
		<form method="post" action="options.php">
		<?php endif;?>
		<div id="<?php echo $optiongroup.'tabs'?>" class="ui-widget">
			<?php settings_fields($optiongroup); ?>
			<?php $tabtitles=array_keys($tabs)?>
			<ul class="ui-corner-none" style="border:none;background:none;">
			<?php foreach($tabtitles as $tabtitle):?>
			<li class="tab" style="border:solid 1px #CCCCCC"><?php HtmlHelper::a($tabtitle,"#".strtolower(str_replace(" ","-",$tabtitle))) ?></li>
			<?php endforeach;?>
			</ul>
			<?php foreach($tabs as $tabtitle => $options):?>
			<div id="<?php echo strtolower(str_replace(" ","-",$tabtitle))?>" class="ui-widget-content ui-corner-top" style="border:solid 1px #797979">
			<?php if(!$saveAllTabs):?>
			<form method="post" action="options.php">
			<?php endif;?>			
			<table class="form-table">
			<?php foreach($options as $key => $option):	
				$type=false;
				$dropdown=false;
				if(is_array($option)){
					$label=$option[0];
					$type=$option[1];		
					if($type==null){
						$type=array_keys($option);
						$type=$type[1];
						$temp=$option[$type];
						if(is_array($temp)){
							$dropdown=$temp;
						}
					}
				}else
					$label=$option;
			?>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo $values?$values[$key]:$key ?>"><?php if(is_int($label)) echo str_replace('_',' ',$values?$values[$key]:$key ); else echo $label ?></label></th>
					<td>
					<?php 
						if($type):
							if($type=='textarea'):?>
								<textarea id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>"><?php echo $values?$values[$key]:get_option($key); ?></textarea>
					<?php 	elseif($type=='checkbox'):?>
								<input type="checkbox" id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" value="1" <?php echo $values?$values[$key]:get_option($key)?'checked="checked"':''; ?> />					
					<?php	elseif(strpos($type,'dropdown')!==false):?>
					<?php 		$selected=$values?$values[$key]:get_option($key);	?>
								<select id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>">
					<?php 		foreach($dropdown as $text => $value):?>
					<?php 			
									if($selected==$value)
										$selected=' selected="selected" ';?>
									<option  <?php echo $selected ?>value="<?php echo $value; ?>">
										<?php if(is_int($text)): echo $value; else: echo $text; endif; 	echo '  '.$text.' '.$value;?>
									</option>
					<?php 		endforeach;?>
								</select>
					<?php 	endif;?>
					<?php else:?>
								<input type="text" id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" value="<?php echo $values?$values[$key]:get_option($key); ?>" />
					<?php endif;?>
					</td>
				</tr>
			<?php endforeach;?>
			</table>
								<p class="submit">
			<input type="submit" class="button-primary" value="<?php $saveAllTabs?_e('Save All Changes'):_e('Save Changes'); ?>" />
			</p>	
			<?php if(!$saveAllTabs):?>
			</form>
			<?php endif;?>			
			</div>
			<?php endforeach;?>		
		</div>					
			<?php if(!$saveAllTabs):?>
			</form>
			<?php endif;?>			
		<script type="text/javascript">
jQuery(function(){
	jQuery("#<?php echo $optiongroup.'tabs' ?>").tabs();
});
</script>
			<?php 
	}
	/**
	    $optiongroup=name of the optionsgroup you want to generate a form, for. If isarray optiongroup is the name of the option with the array.
		$options=array('Label'=>'key');
		if $optionNames is not supplied the function will retrieve the options it self and use the optionkey as label.
	 */
	static function simpleOptionsForm($optiongroup,$options=false,$isarray=false){		
		if(!$options){
			$options=array();
			global $new_whitelist_options;
			$options=$new_whitelist_options[$optiongroup];
		}
		$values=false;
		if($isarray)
			$values=get_option($optiongroup);
		?>
		<form method="post" action="options.php">
			<?php settings_fields($optiongroup); ?>
			<table class="form-table">
			<?php foreach($options as $key => $option):	
				$type=false;
				$dropdown=false;
				if(is_array($option)){
					$label=$option[0];
					$type=$option[1];		
					if($type==null){
						$type=array_keys($option);
						$type=$type[1];
						$temp=$option[$type];
						if(is_array($temp)){
							$dropdown=$temp;
						}
					}
				}else
					$label=$option;
			?>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo $values?$values[$key]:$key ?>"><?php if(is_int($label)) echo str_replace('_',' ',$values?$values[$key]:$key ); else echo $label ?></label></th>
					<td>
					<?php 
						if($type):
							if($type=='textarea'):?>
								<textarea id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>"><?php echo $values?$values[$key]:get_option($key); ?></textarea>
					<?php 	elseif($type=='checkbox'):?>
								<input type="checkbox" id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" value="1" <?php echo $values?$values[$key]:get_option($key)?'checked="checked"':''; ?> />					
					<?php	elseif(strpos($type,'dropdown')!==false):?>
					<?php 		$selected=$values?$values[$key]:get_option($key);	?>
								<select id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>">
					<?php 		foreach($dropdown as $text => $value):?>
					<?php 			
									if($selected==$value)
										$selected=' selected="selected" ';?>
									<option  <?php echo $selected ?>value="<?php echo $value; ?>">
										<?php if(is_int($text)): echo $value; else: echo $text; endif; 	echo '  '.$text.' '.$value;?>
									</option>
					<?php 		endforeach;?>
								</select>
					<?php 	endif;?>
					<?php else:?>
								<input type="text" id="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" name="<?php echo $values?$optiongroup.'['.$key.']':$key  ?>" value="<?php echo $values?$values[$key]:get_option($key); ?>" />
					<?php endif;?>
					</td>
				</tr>
			<?php endforeach;?>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form><?php 
	}
	static function registerSettings($optiongroup,$optionkeys){
		foreach($optionkeys as $key)
			register_setting($optiongroup,$key);
	}
	static function ullinklist($id=false,$listitems,$title=false,$linkmethod=false,$textmethod=false,$path){
		$list='<ul';
		if($id)
			$list.=' id="'.$id.'"';
		$list.='>';
		if($title)
			$list.=$title;
		foreach($listitems as $listitem){
			$link;
			$text;
			if($linkmethod)
				$link=$listitem->$linkmethod();
			if($textmethod)
				$text=$listitem->$textmethod();
			$list.='<li><a href="'.$path.$link.'">'.$text.'</a></li>';
		}
		$list.='</ul>';
		return $list;
	}
	static function insertPage($title,$slug,$content,$status='draft',$author=false){
		global $wpdb,$wp_rewrite;		
		$page= array();
		global $user_ID;
      	get_currentuserinfo();
      	$user=$author?$author:$user_ID;
		$query = "INSERT $wpdb->posts SET post_title='$title',
											post_name='$slug',
											post_content='$content',
											post_parent='0',
											post_author='$user',
											post_status='$status',
											post_type='page',
											post_date=now(),
											post_date_gmt=utc_timestamp(),
											post_modified=now(),
											post_modified_gmt=utc_timestamp(),
											comment_status='closed',
											ping_status='closed',
											post_excerpt='',
											to_ping='',     
											pinged='',      
											post_content_filtered='',
											menu_order=0";

										
		$wpdb->query($query);	
		$wp_rewrite->flush_rules();	
	}
}
?>