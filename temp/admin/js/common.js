function get_job()
{
	var tid = $("#tid").val();
	$("#jid").load("/?action=ajax_get_job&tid="+tid);
}

function get_institution()
{
	var tid = $("#tid").val();
	$("#iid").load("/?action=ajax_get_institution&tid="+tid);
}
	
$(function(){	
	var tid = $("#tid").val();
	
	if (tid != "0")
	{
		var jid = $("#select_jid").val();
		$("#jid").load("/?action=ajax_get_job&tid=" + tid +"&jid=" + jid);
		
		var iid = $("#select_iid").val();
		$("#iid").load("/?action=ajax_get_institution&tid=" + tid +"&iid=" + iid);
	}
});