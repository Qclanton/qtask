$.fn.selectRange = function(start, end) {
    if(!end) end = start; 
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

$(function() {				
	function synchAreas(main_block, editor_wrapper) {
		var id = editor_wrapper.split("--")[1];
		
		if (main_block == "textarea") {
			$("#editor-content--" + id + " div p").html($("#" + editor_wrapper + " .editor-textarea").val());
		} else {
			$("#" + editor_wrapper + " .editor-textarea").html($("#editor-content--" + id).html());
		}	
	}
	
	$(".editor-wrapper .editor-textarea").on("blur keyup paste input", function() {
		var editor_wrapper = $(this).parent().attr('id');
		
		synchAreas("textarea", editor_wrapper);
	});
	
	
	$(".editor-wrapper .editor-textarea").keypress(function(event) {
		if (event.which == 13) {	
			var caret_position = $(this).selection('getPos').end;
			var text = $(this).val();
			var last_tag = substring = text.substr(caret_position-5, 5);
			var next_tag = substring = text.substr(caret_position, 5);
			if (last_tag == "</li>" || next_tag == "</li>") {
				if (next_tag == "</li>") { $(this).selectRange(caret_position+5); }
				
				$(this)
					.selection('insert', {text: '\n\t<li></li>', mode: 'before'})
					.selectRange(caret_position+6);
				
				if (next_tag == "</li>") { $(this).selectRange(caret_position+11); }	
				
				return false;					
			}
			else {			
				$(this)
					.selection('insert', {text: '<br />', mode: 'before'});
			}
		}
	});
	
	
	// Button functions		
	$(".editor-wrapper .editor-toolbar .editor-button-bold").on("click", function() {
		var editor_wrapper = $(this).parent().parent().attr('id');
					
		$("#" + editor_wrapper + " .editor-textarea")
			.selection('insert', {text: '<b>', mode: 'before'})
			.selection('insert', {text: '</b>', mode: 'after'});
			
			
		synchAreas("textarea", editor_wrapper);
	});

	
	
	$(".editor-wrapper .editor-toolbar .editor-button-italic").on("click", function() {
		var editor_wrapper = $(this).parent().parent().attr('id');
		
		$("#" + editor_wrapper + " .editor-textarea")
			.selection('insert', {text: '<i>', mode: 'before'})
			.selection('insert', {text: '</i>', mode: 'after'});
			
			
		synchAreas("textarea", editor_wrapper);
	});

	
	$(".editor-wrapper .editor-toolbar .editor-button-quote").on("click", function() {
		var editor_wrapper = $(this).parent().parent().attr('id');
		
		$("#" + editor_wrapper + " .editor-textarea")
			.selection('insert', {text: '<blockquote>', mode: 'before'})
			.selection('insert', {text: '</blockquote>', mode: 'after'});
			
			
		synchAreas("textarea", editor_wrapper);
	});
	
	$(".editor-wrapper .editor-toolbar .editor-button-code").on("click", function() {
		var editor_wrapper = $(this).parent().parent().attr('id');
		
		$("#" + editor_wrapper + " .editor-textarea")
			.selection('insert', {text: '<code>', mode: 'before'})
			.selection('insert', {text: '</code>', mode: 'after'});
			
			
		synchAreas("textarea", editor_wrapper);
	});
	
	$(".editor-wrapper .editor-toolbar .editor-button-bullet_list").on("click", function() {
		var editor_wrapper = $(this).parent().parent().attr('id');
		
		$("#" + editor_wrapper + " .editor-textarea")
			.selection('insert', {text: '\n<ul>\n\t<li></li>\n</ul>\n', mode: 'after'});
			
			
		synchAreas("textarea", editor_wrapper);
	});
	
	$(".editor-wrapper .editor-toolbar .editor-button-numbered_list").on("click", function() {
		var editor_wrapper = $(this).parent().parent().attr('id');
				
		$("#" + editor_wrapper + " .editor-textarea")
			.selection('insert', {text: "\n<ol>\n\t<li></li>\n</ol>\n", mode: 'after'});
			
			
		synchAreas("textarea", editor_wrapper);
	});
	
	$(".editor-wrapper .editor-toolbar .editor-button-result").on("click", function() {
		var id = $(this).parent().parent().attr('id').split("--")[1];
		
		if ($(this).hasClass("edtitor-result-opened")) {
			$("#editor-content--" + id).hide();
			$(this).removeClass("edtitor-result-opened");
		}
		else {
			$("#editor-content--" + id).show();
			$(this).addClass("edtitor-result-opened");	
		}
	});
	// End of button functions		 
});
