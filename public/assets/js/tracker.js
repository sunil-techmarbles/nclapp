function startStop() {
    if(isRunning)
    {
        stopTimer();
    }
    else
    {
        startTimer();
    }
}

function startTimer() {
    if(action!=="")
    {
        prevAction = action;
    }
    action = $('#act').val();
    if (action=="")
    {
        alert("Please select Activity");
        return false;
    }
    $('#act').attr('disabled',true);
    isRunning = true;
    runTime = 0;
    pauseTime = 0;
    isPause=false;
    startTime = new Date().getTime();
    intTime = new Date().toISOString();
    $("#startbtn").hide(); 
    $("#timer").show();
    $('#time').html('0:00:00<span class="ms">.0</span>');
    setTimeout(updTime,100);
    var p = $('#playPause');
    if(p.hasClass('fa-play'))
    {
        p.removeClass('fa-play');
        p.addClass('fa-pause');
    }
}

function stopTimer() {
    isRunning = false;
    if($('#multiitems').prop('checked'))
    {
        var itmcount = prompt("How many items have you processed?", "1");
        if (itmcount === null || isNaN(itmcount)) itmcount = 1;
    }
    else
    {
        var itmcount =1;
    }
    if(runTime > 5000)
    {
        $.get("/"+prefix+"/timetracker",{action:'timeTracker',start:intTime,duration:Math.round(runTime/1000),act:action,count:itmcount},function(result){
            console.log(result);
            startTimer();
        }); 
    }
    else
    {
        $('#time').html('0:00:00<span class="ms">.0</span>');
    }
}

function ppTime() {
    var p = $('#playPause');
    if(p.hasClass('fa-pause'))
    {
        p.removeClass('fa-pause');
        p.addClass('fa-play');
        isPause = true;
        pstartTime = new Date().getTime();
    }
    else
    {
        p.removeClass('fa-play');
        p.addClass('fa-pause');
        isPause=false;
    }
}

function stopTime() {
    isRunning = false;
    $('#act').attr('disabled',false);
    $('#time').html('0:00:00<span class="ms">.0</span>');
    if($('#multiitems').prop('checked'))
    {
        var itmcount = prompt("How many items have you processed?", "1");
        if (itmcount === null || isNaN(itmcount)) itmcount = 1;
    }
    else
    {
        var itmcount =1;
    }
    if(runTime > 5000)
    { 
        $.get("/"+prefix+"/timetracker",{action:'timeTracker',start:intTime,duration:Math.round(runTime/1000),act:action,count:itmcount},function(result){
        }); 
    }
}

function updTime() {
    if(isRunning)
    {
        var n = new Date().getTime();
        if(isPause)
        {
            var p = n - pstartTime;
            pstartTime = n;
            pauseTime += p;
        }
        runTime = n - startTime - pauseTime;
        var t = new Date(runTime).toISOString()
        $('#time').html(t.substr(12,7)+'<span class="ms">'+t.substr(19,2)+'</span>')
        if(isRunning) setTimeout(updTime,98);   
    }
}

function setActivity()
{
    var act = $('#act').val();
    if(act=="Other")
    {
        $('#act').val('');
        var nact = prompt("Please enter Activity Name", "");
        if(nact !== null)
        {
            var o = new Option(nact, nact);
            $(o).html(nact);
            $("#act").append(o);
            $("#act").val(nact);
        }
    }
}

$('.fa-repeat').click(function(){
    if(prevAction!="")
    {
        $('#act').val(prevAction);
    }
});

$('#act').change(function()
{
    if(action=="")
    {
        $('.fa-repeat').css('color','#dddddd');
    }
    else
    {
        $('.fa-repeat').css('color','#139999');
    }
});

$(document).ready(function () {
    if($('input[name="reporttracker"]').val() != '')
    {
        $("#dates").daterangepicker({
            opens: 'left'
            }, function (start, end, label) {
                setTimeout(doFilter, 500);
            }
        );
        $(".usr-chart").hBarChart();
        $(".dusr-chart").hBarChart();
        $(".ausr-chart").hBarChart();
        $(".act-chart").hBarChart();
        $(".aact-chart").hBarChart();
        $(".dact-chart").hBarChart();
    }
});

if($('input[name="reporttracker"]').val() != '')
{
    function doFilter() {
        $("#statsform").submit();
    }
}