<button id="connectBtn">Connect WhatsApp</button>
<button id="restartBtn">Restart</button>
<div id="qrBox"></div>
<div id="statusBox"></div>

<hr>

<input type="text" id="phone" placeholder="919999999999">
<textarea id="message"></textarea>
<button id="sendBtn">Send</button>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$('#connectBtn').click(function(){
    $.post('/whatsapp/create',{_token:'{{ csrf_token() }}'},function(){
        loadQR();
    });
});

function loadQR(){
    $.get('/whatsapp/qr',function(res){
        if(res.qr){

            $('#qrBox').html(
                '<img width="250" src="'+res.qr+'">'
            );

            checkStatus();
        }
    });
}

$('#deleteBtn').click(function(){
    if(confirm("Are you sure?")){
        $.post('/whatsapp/delete',{
            _token:'{{ csrf_token() }}'
        },function(res){
            alert("Instance deleted");
            location.reload();
        });
    }
});

$('#restartBtn').click(function(){
    $.post('/whatsapp/restart',{
        _token: '{{ csrf_token() }}'
    }, function(res){
        alert('Restarted');
    });
});


function checkStatus(){
    setInterval(function(){
        $.get('/whatsapp/status',function(res){
            if(res.instance && res.instance.state){
                $('#statusBox').html(res.instance.state);
                if(res.instance.state === "open"){
                    alert("Connected!");
                }
            }
        });
    },5000);
}

$('#sendBtn').click(function(){
    $.post('/whatsapp/send',{
        phone:$('#phone').val(),
        message:$('#message').val(),
        _token:'{{ csrf_token() }}'
    },function(){
        alert("Sent");
    });
});

</script>