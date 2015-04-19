function EditNoteCntrl()
{
	init();

	function init()
	{
		$('#e_note').editable({
			type: 'textarea',
			toggle: 'manual',
			url: './ccny/scidiv/cores/ctrl/changeNote.php',
			title: 'Session notes:',
			pk: getRecordID,
			disabled: false,
			ajaxOptions: {
			    dataType: 'json'
			},
			success: function(response, newValue) {


				if( response.hasOwnProperty('error'))
				{
					if( response.error == 1 )
						return response.message;
				}
				else
					return "Invalid server response";

			}
		});

		$('#edit_note_btn').click(function(e)
			{
				e.stopPropagation();
				$('#e_note').editable('toggle');
		});
	};


	function getRecordID()
	{
		return $('input#s_rec_id').val();
	};

	function destroy()
	{
		$('#edit_note_btn').off();
	}

	return{
		destroy: destroy
	}
};

module_handler.addModule('EDIT_NOTE', new EditNoteCntrl());
