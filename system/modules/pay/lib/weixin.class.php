<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . "weixin" . DIRECTORY_SEPARATOR . "WxPayPubHelper.php";

class weixin {

	private $config;
	private $db;

	/**
	 * 	支付入口
	 * */
	public function config($config = null) {
		$this->config = $config;
	}

	public function send_pay() {
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		$amount = trim($this->config['money']) * 100;

		$notify_url = $this->config['NotifyUrl'];   //通知URL
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		//iconv("gb2312","utf-8//IGNORE",
		$unifiedOrder->setParameter("body", $this->config['title']); //商品描述
		//自定义订单号，此处仅作举例
		$out_trade_no = $this->config['code'];
		$unifiedOrder->setParameter("out_trade_no", $out_trade_no); //商户订单号 
		$unifiedOrder->setParameter("total_fee", $amount); //总金额
		$unifiedOrder->setParameter("notify_url", $notify_url); //通知地址 
		$unifiedOrder->setParameter("trade_type", "NATIVE"); //交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
		$unifiedOrder->setParameter("attach", "111"); //附加数据 
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
		//获取统一支付接口结果
		$unifiedOrderResult = $unifiedOrder->getResult();
//	var_dump($unifiedOrderResult);exit;
		//商户根据实际情况设置相应的处理流程
		if ($unifiedOrderResult["return_code"] == "FAIL") {
			//商户自行增加处理流程
			echo "通信出错：" . $unifiedOrderResult['return_msg'] . "<br>";
		} elseif ($unifiedOrderResult["result_code"] == "FAIL") {
			//商户自行增加处理流程
			echo iconv("utf-8", "gb2312//IGNORE", "错误代码：" . $unifiedOrderResult['err_code'] . "<br>");
			echo iconv("utf-8", "gb2312//IGNORE", "错误代码描述：" . $unifiedOrderResult['err_code_des'] . "<br>");
		} elseif ($unifiedOrderResult["code_url"] != NULL) {
			$qrcode = 'http://www.1ybye.com/system/modules/pay/lib/qrcode.js';
			//从统一支付接口获取到code_url
			$code_url = $unifiedOrderResult["code_url"];
			$saomiao = "微信安全支付，请扫我";
			//		//参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
			if ($unifiedOrderResult["code_url"] != NULL) {
				$hehe = '	var url = "' . $code_url . '";var qr = qrcode(10, "M");qr.addData(url);qr.make();var wording=document.createElement("p");wording.innerHTML = "' . $saomiao . '";	var code=document.createElement("DIV");	code.innerHTML = qr.createImgTag();var element=document.getElementById("qrcode");
					element.appendChild(wording);element.appendChild(code);';
			}


			$def_url = '<html><head><script src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script></head><body><div align="center" id="qrcode">
		</div><input type="hidden" id="code" value='.$out_trade_no.'></div></body><script src="' . $qrcode . '"></script><script>' . $hehe .'</script>';
			
			//$def_url.='<script>$(document).ready(function () { setInterval("ajaxstatus()", 3000); }); function ajaxstatus() { if ($("#out_trade_no").val() != 0) { $.ajax({ url: "/?/member/cart/orderstatus?tradeno=" + $("#out_trade_no").val(), type: "GET", dataType:"json", data: "", success: function (data) { // alert(data); if (data==1) { window.location.href = "/?/member/home/userbuylist"; //页面跳转 } },  }); } }</script></html>';
			$def_url.='<script>$(document).ready(function(){setInterval("ajaxstatus()",1000)});function ajaxstatus(){if($("#code").val()!=0){$.ajax({url:"?/member/cart/orderstatus",type:"post",data:{code:$("#code").val()},success:function(data){if(data == 1){window.location.href="?/member/home/userbuylist"}},})}};</script></html>';
					
					
			echo $def_url;
			
			exit;
			//商户自行增加处理流程
			//......
		}
	}



}

?>