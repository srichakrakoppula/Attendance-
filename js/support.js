$('select').on('change', function() {
			var pick = this.value;
			var totCls = $('#total_classes').attr('value');
			var totPre = $('#total_present').attr('value');
			var perc = totPre/(totCls+pick);
			if(perc>=75){
				$('#gre75').html('You can bunk the coming '+pick+' classes');
			}
});