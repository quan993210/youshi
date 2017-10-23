	function web_share(share_type, url, url_suffix, image_path, contentID)
	{
		if (url == "")url = location.href;
		
		if (url_suffix != "")
		{
			if (url.indexOf(".php?") != -1)
			{
				url = url + "&" + url_suffix;
			}
			else if (url.indexOf(".php") != -1)
			{
				url = url + "?" + url_suffix;
			}
			else
			{
				url = url + "index.php?" + url_suffix;
			}
		}
		
		if (image_path != "")
			image_path = "http://" + document.domain + image_path;
		
		var text = "";
		if (contentID != "")
			text = document.getElementByID(contentID).value;
		else
			text = document.title;
		
		var content = text + url;
		switch(share_type)
		{
			case "qzone":
				window.open('http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+encodeURIComponent(url)+'&rcontent='+encodeURIComponent(content),'_blank','scrollbars=no,width=600,height=450,left=75,top=20,status=no,resizable=yes'); void 0;
				break;
			case "kaixin001":
				window.open('http://www.kaixin001.com/~repaste/repaste.php?rtitle='+encodeURIComponent(text)+'&rurl='+encodeURIComponent(url)+'&rcontent='+encodeURIComponent(content),'_blank','scrollbars=no,width=600,height=450,left=75,top=20,status=no,resizable=yes'); void 0;
				break;
			case "renren":
				window.open('http://share.renren.com/share/buttonshare.do?link='+url+'&title='+encodeURIComponent(text),'_blank','scrollbars=no,width=600,height=450,left=75,top=20,status=no,resizable=yes');void(0);
				break;
			case "douban":
				var u='http://www.douban.com/recommend/?url='+url+'&title='+encodeURIComponent(text);window.open(u,'douban','toolbar=0,resizable=1,scrollbars=yes,status=1,width=450,height=330');void(0);
				break;
			case "tsina":
				void((function(s,d,e,r,l,p,t,z,c){var f='http://v.t.sina.com.cn/share/share.php?c=spr_web_bd_pingan_weibo',u=z||d.location,p=['&url=',e(u),'&title=',e(t||d.title),'&source=',e(r),'&sourceUrl=',e(l),'&content=',c||'gb2312','&pic=',e(p||'')].join('');function a(){if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=440,height=430,left=',(s.width-440)/2,',top=',(s.height-430)/2].join('')))u.href=[f,p].join('');};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else a();})(screen,document,encodeURIComponent,'','', image_path, content,url,'页面编码gb2312|utf-8默认gb2312'));
				break;
			case "tqq":
				window.open('http://v.t.qq.com/share/share.php?title='+encodeURIComponent(text)+'&url='+encodeURIComponent(url)+'&source=bookmark','_blank','scrollbars=no,width=600,height=450,left=75,top=20,status=no,resizable=yes'); void 0;
				break;
		}
	}