$(function(){
	$("img.lazy").lazyload({effect: "fadeIn"});
})
//加入收藏夹
function AddFavorite()
{
	var sURL = "http://www.hascendhardware.com";
	var sTitle = "hascendhardware";
    try
    {
        window.external.addFavorite(sURL, sTitle);
    }
    catch (e)
    {
        try
        {
            window.sidebar.addPanel(sTitle, sURL, "");
        }
        catch (e)
        {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}

//设为首页
function SetHome(obj)
{
        try{
                obj.style.behavior='url(#default#homepage)';obj.setHomePage("http://www.hascendhardware.com");
        }
        catch(e){
                if(window.netscape) {
                        try {
                                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
                        }
                        catch (e) {
                                alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");
                        }
                        var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
                        prefs.setCharPref('browser.startup.homepage', window.location);
                 }
        }
}

function get_radio_val(obj)
{
	var val = $("input[name='"+ obj +"']:checked").val();
	return val;
}


/*----------------------以下为封装的插件------------------------------*/

//插件名称：fadeBg
//功能：淡入淡出插件
;(function($) {
	$.fn.extend({
		"fadeBg":function(options){
			//设置默认值
			options=$.extend({
				easing:"swing", //动画效果
				stopTime:4000, //停留时间
				time:1500, //动画时间
				mouseStop:true,//鼠标经过停止                
				callback:function(){}//回调函数，返回当前索引
			},options);
			
			 //插件实现代码 
			var obj=$(this); 
			//var objdiv=obj.find("div");
			var objdiv=obj.children();
			var length=objdiv.length;
			objdiv.css("display","none");
			objdiv.first().css("display","block");
			var i=0;
			if(length>1){
				function execute(){
					if(!objdiv.is(":animated")){
						if(i<length-1){
							objdiv.eq(i).fadeOut(options.time,options.easing);	
							objdiv.eq(i+1).fadeIn(options.time,options.easing);	
							i++;										
						}else{
							objdiv.eq(i).fadeOut(options.time,options.easing);																	
							objdiv.eq(0).fadeIn(options.time,options.easing);	
							i=0;	
						}
											
						//回调函数
						if(options.callback){
							options.callback(i);
						}						
					}
				}		
				var timer=setInterval(execute,options.stopTime);
				if(options.mouseStop==true){														
					objdiv.mouseover(function(){	
						clearInterval(timer);								
					}).mouseleave(function(){
						timer=setInterval(execute,options.stopTime);
					})
				}
			}
			return this;  //返回this，使方法可链。
		}
	});
})(jQuery);

//插件名称：scrollList
//功能：横向滚动插件
;(function($) {
	$.fn.extend({
		"scrollList":function(options){
			//设置默认值
			options=$.extend({
				easing:"swing", //动画效果
				stopTime:4000, //停留时间
				time:800, //动画时间
				mouseStop:true,//鼠标经过停止                
				callback:function(){}//回调函数，返回当前索引
			},options);
			
			 //插件实现代码 
			var obj=$(this); 
			var objdiv=obj.children();
			var length=objdiv.length;
			var width=objdiv.first().outerWidth();
			var i=0;
			if(length>1){				
				function execute(){
					if(!objdiv.is(":animated")){
						if(i<length-1){
							obj.animate({"left":"-="+width},options.time,options.easing);
							i++;										
						}else{							
							obj.animate({"left":0},options.time,options.easing);
							i=0;	
						}
											
						//回调函数
						if(options.callback){
							options.callback(i);
						}						
					}
				}		
				var timer=setInterval(execute,options.stopTime);
				if(options.mouseStop==true){														
					objdiv.mouseover(function(){	
						clearInterval(timer);								
					}).mouseleave(function(){
						timer=setInterval(execute,options.stopTime);
					})
				}
			}
			return this;  //返回this，使方法可链。
		}
	});
})(jQuery);




function web_share(share_type)
{
	var url = location.href;
	var text = document.title;
	var image_path = "";
	switch(share_type)
	{
			case "qzone":
				window.open('http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+encodeURIComponent(url)+'&rcontent='+encodeURIComponent(text),'_blank','scrollbars=no,width=600,height=450,left=75,top=20,status=no,resizable=yes'); void 0;
				break;
			case "tsina":
				void((function(s,d,e,r,l,p,t,z,c){var f='http://v.t.sina.com.cn/share/share.php?c=spr_web_bd_pingan_weibo',u=z||d.location,p=['&url=',e(u),'&title=',e(t||d.title),'&source=',e(r),'&sourceUrl=',e(l),'&content=',c||'gb2312','&pic=',e(p||'')].join('');function a(){if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=440,height=430,left=',(s.width-440)/2,',top=',(s.height-430)/2].join('')))u.href=[f,p].join('');};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else a();})(screen,document,encodeURIComponent,'','', image_path, text, url, '页面编码gb2312|utf-8默认gb2312'));
				break;
			case "tqq":
				window.open('http://v.t.qq.com/share/share.php?title='+encodeURIComponent(text)+'&url='+encodeURIComponent(url)+'&source=bookmark','_blank','scrollbars=no,width=600,height=450,left=75,top=20,status=no,resizable=yes'); void 0;
				break;
			case 'weixin':
				url = encodeURIComponent(url);
				var src = "http://qr.liantu.com/api.php?text=" + url;
				//alert(url);
				$.layer({
					type: 2,
					shadeClose: true,
					title: false,
					closeBtn: [0, false],
					shade: [0.8, '#000'],
					border: [0],
					offset: ['',''],
					area: ['300px', '300px'],
					iframe: {src: src}
				}); 
				break;	
	}
}