/* $Id: listtable.js 14980 2008-10-22 05:01:19Z testyang $ */

if (typeof Utils != 'object')
{
  alert('Utils object doesn\'t exists.');
}

var listTable = new Object;

listTable.query = "query";
listTable.filter = new Object;
//listTable.url = location.href.lastIndexOf("?") == -1 ? location.href.substring((location.href.lastIndexOf("/")) + 1) : location.href.substring((location.href.lastIndexOf("/")) + 1, location.href.lastIndexOf("?"));
//listTable.url += "?is_ajax=1";
listTable.url="ajaxhtml/process.php";

/**
 * 创建一个可编辑区
 */
listTable.edit = function(obj, tbl, col, id)
{
  var tag = obj.firstChild.tagName;
  if (typeof(tag) != "undefined" && tag.toLowerCase() == "input")
  {
    return;
  }

  /* 保存原始的内容 */
  var org = obj.innerHTML;
  var val = Browser.isIE ? obj.innerText : obj.textContent;

  /* 创建一个输入框 */
  var txt = document.createElement("INPUT");
  txt.value = (val == 'N/A') ? '' : val;
  txt.style.width = (obj.offsetWidth + 12) + "px" ;

  /* 隐藏对象中的内容，并将输入框加入到对象中 */
  obj.innerHTML = "";
  obj.appendChild(txt);
  txt.focus();

  /* 编辑区输入事件处理函数 */
  txt.onkeypress = function(e)
  {
    var evt = Utils.fixEvent(e);
    var obj = Utils.srcElement(e);

    if (evt.keyCode == 13)
    {
      obj.blur();

      return false;
    }

    if (evt.keyCode == 27)
    {
      obj.parentNode.innerHTML = org;
    }
  }

  /* 编辑区失去焦点的处理函数 */
  txt.onblur = function(e)
  {
    if (Utils.trim(txt.value).length > 0 && txt.value != org)
    {
	  var val = Utils.trim(txt.value);
	  
	  $.post(
			  listTable.url, 
			  { action: "mod_col", tbl: tbl, col: col, val: val, id: id }, 
			  function(data)
			  { 
				  if (parseInt(data) == 0)
					  obj.innerHTML = txt.value;
				  else
				  {
					  alert("修改失败，代码："+data);
				  	  obj.innerHTML = org;
				  }	  
			  }
			 );  
    }
    else
    {
      obj.innerHTML = org;
    }
  }
}

/**
 * 切换状态
 */
listTable.toggle = function(obj, tbl, col, id)
{
  var admin_temp_path = $("#admin_temp_path").val();
  var val = (obj.src.match(/yes.gif/i)) ?  0: 1;
  
  $.post(
		  listTable.url, 
		  { action: "mod_col", tbl: tbl, col: col, val: val, id: id }, 
		  function(data)
		  { 
			  if (parseInt(data) == 0)
			  	  obj.src = (val == 0) ? (admin_temp_path + '/images/no.gif') : (admin_temp_path + '/images/yes.gif');	
			  else
				  alert("修改失败，代码："+data);  
		  }
		 );
}

listTable.selectAll = function(obj, chk)
{
  if (chk == null)
  {
    chk = 'checkboxes';
  }

  var elems = obj.form.getElementsByTagName("INPUT");

  for (var i=0; i < elems.length; i++)
  {
    if (elems[i].name == chk || elems[i].name == chk + "[]")
    {
      elems[i].checked = obj.checked;
    }
  }
}