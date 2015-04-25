function CancelSessionCntrl()
{
    init();

    function init()
    {
        $('#cancel_btn').click(function ()
        {
            var record_id = getRecordID();

            var data = {'id': record_id,timestamp: getTimestamp};

            $.ajax({
                type: 'POST',
                url: './ccny/scidiv/cores/ctrl/cancelEvent.php',
                dataType: 'json',
                data: data,
                cache: false,
                success: function (data)
                {
                    if (data.hasOwnProperty('error'))
                    {
                        if (data.error == 1)
                        {
                            alert(data.message);

                        }
                        else
                        {

                            clearUI();
                            deleteEvent(record_id);
                            notifySuccess("Event cancelled.");

                        }
                    }
                    else
                    {
                        notifyError('Error: Invalid reponse from the server. Operation failed');

                    }

                },
                error: function ()
                {
                    notifyError('Cancel operation failed. Please check your connection and try again');

                }
            });
        });

    }
    ;

    function getRecordID()
    {
        return $('input#s_rec_id').val();
    };
    
    function getTimestamp()
    {
        return $('input#s_timestamp').val();
    };

    function destroy()
    {
        $('#cancel_btn').off();
    }

    return{
        destroy: destroy
    }

}
;

module_handler.addModule('CANCEL_SESSION', new CancelSessionCntrl());