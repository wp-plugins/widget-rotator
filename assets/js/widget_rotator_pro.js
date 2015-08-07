jQuery(document).ready(function($) {

//Calculate the top limit available for each widget

	function limit_random_pri(){
		$('#widgets-right').on('keyup','input[name="random_priority"]',function(e){
			var val = Math.abs(parseInt($(this).val()));
			if(isNaN(val)){
				val = 0;
			}
			$(this).val(val);
			var tops = $(this).parent().next('p.howto').find('.tops').text();
			if( tops && val>tops)
				$(this).val(tops);
		});
	}

//Calculate the priority of a widget before it is displayed

	function widget_rotator_pro_pri_check(){
		$('#widgets-right').on('click mouseover','input[name="random_priority"]',function(e){
			var rotate_in = $(this).parents('div.widget').find('select[name="rotate_in"]').find(":selected").val();
			var self_val = $(this).val();
			if(rotate_in != -1){
				
				var sum = 0;
				$('#widgets-right select[name="rotate_in"]').each(function(e){
					if(rotate_in == $(this).find(":selected").val()){
						var val = $(this).parents('div.widget').find('input[name="random_priority"]').val();
						sum = sum+Math.abs(parseInt(val));
					}
				});
				sum = sum - self_val;
				if(sum>=0 && sum<=100){
					var tops = 100 - sum;
					$(this).parent().next('p.howto').html("You can enter <strong class='tops'>"+tops+'</strong> tops.');
				}

			}
			
		});
	}

//Check the position of the widget in a rotator

	function widget_rotator_pro_pos_check(){
		

		$('#widgets-right').on('click','select[name="rotate_in"]',function(e){
			var selected = $(this).find(":selected").text();
			var val = $(this).find(":selected").val();
			var sidebar = $(this).parents('div.widgets-sortables');
			if(val != -1){
				if(sidebar.find("div[id$='widget-rotator-pro-"+val+"']").length == 0){
					alert(selected + " is in other sidebar!");
					$(this).find('option[value="-1"]').attr("selected", "selected")
					e.preventDefault();
				}

			}
		});
		
	}
	widget_rotator_pro_pos_check();
	widget_rotator_pro_pri_check();
	limit_random_pri();
});
