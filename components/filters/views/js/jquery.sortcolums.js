$(function() {
	$.moveColumn = function (table, from, to) {
		var rows = $('tr', table);
		var cols;
		
		rows.each(function() {
			cols = $(this).children('th, td');			
			if (from > to) {			
				cols.eq(from).detach().insertBefore(cols.eq(to));
			}
			else if (from < to) {
				cols.eq(from).insertAfter(cols.eq(to));
			}
		});
	}
});	
