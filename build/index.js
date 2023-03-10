!function(){"use strict";var e,a={237:function(){var e=window.wp.blocks,a=window.wp.element,t=window.wp.i18n,l=window.wp.blockEditor,n=window.wp.data,r=window.wp.components;(0,e.registerBlockType)("amateur-tv/feed",{edit:function(e){(0,l.useBlockProps)();const{attributes:o,setAttributes:m}=e,{usernameColor:i,lang:u,liveColor:s,displayLive:c,displayTopic:v,displayGenre:g,displayUsers:p,bgColor:h,genre:_,age:d,topicColor:b,link:C,targetNew:w,labelBgColor:f,imageWidth:E,imageHeight:y,columnGap:S,autoRefresh:k,api:x,count:T,textShadowColor:P,textShadowValue:N}=o,O="1px 1px",[I,L]=(0,a.useState)(!0),[R,B]=(0,a.useState)(null),[A,j]=(0,a.useState)(new URL(x)),F=e=>{let a=A,t=e.val;e.multiple&&(t=t.join(",")),t?a.searchParams.set(e.name,t):a.searchParams.delete(e.name),B(null),j(A),L(!0)},G=(0,n.useSelect)((e=>{var a;let t=null===(a=e("core").getSite())||void 0===a?void 0:a.language;return t&&t.split("_")[0]}));return(0,a.useEffect)((()=>{I&&fetch(A,{method:"GET"}).then((e=>e.json())).then((e=>{L(!1),B(e.body)})).catch((e=>console.error(e)))}),[I,A]),(0,a.createElement)(a.Fragment,null,(0,a.createElement)(l.InspectorControls,null,(0,a.createElement)(r.PanelBody,{title:(0,t.__)("Filters","amateur-tv"),initialOpen:!0},(0,a.createElement)(r.SelectControl,{label:(0,t.__)("Gender","amateur-tv"),value:_,multiple:!0,options:[{label:(0,t.__)("Woman","amateur-tv"),value:"W"},{label:(0,t.__)("Couple","amateur-tv"),value:"C"},{label:(0,t.__)("Man","amateur-tv"),value:"M"},{label:(0,t.__)("Trans","amateur-tv"),value:"T"}],onChange:e=>{m({genre:e}),F({name:"genre",val:e,multiple:!0})}}),(0,a.createElement)(r.SelectControl,{label:(0,t.__)("Age","amateur-tv"),value:d,multiple:!0,options:[{label:"18-22",value:"18-22"},{label:"23-29",value:"23-29"},{label:"29-39",value:"29-39"},{label:"40",value:"40"}],onChange:e=>{m({age:e}),F({name:"age",val:e,multiple:!0})}}),(0,a.createElement)(r.RangeControl,{label:(0,t.__)("Number of cams","amateur-tv"),value:T,initialPosition:R?R.length:0,onChange:e=>{m({count:e})},min:0,max:R?R.length:0})),(0,a.createElement)(r.PanelBody,{title:(0,t.__)("Display Settings","amateur-tv"),initialOpen:!0},(0,a.createElement)(r.SelectControl,{label:(0,t.__)("Language","amateur-tv"),value:u||G,options:[{label:(0,t.__)("English","amateur-tv"),value:"en"},{label:(0,t.__)("Spanish","amateur-tv"),value:"es"},{label:(0,t.__)("French","amateur-tv"),value:"fr"},{label:(0,t.__)("German","amateur-tv"),value:"de"}],onChange:e=>{m({lang:e}),F({name:"lang",val:e,multiple:!1})}}),(0,a.createElement)(r.ToggleControl,{label:(0,t.__)("Show Live Label","amateur-tv"),checked:!!c,onChange:e=>{m({displayLive:!c})}}),(0,a.createElement)(r.ToggleControl,{label:(0,t.__)("Show Gender","amateur-tv"),checked:!!g,onChange:e=>{m({displayGenre:!g})}}),(0,a.createElement)(r.ToggleControl,{label:(0,t.__)("Show Users","amateur-tv"),checked:!!p,onChange:e=>{m({displayUsers:!p})}}),(0,a.createElement)(r.ToggleControl,{label:(0,t.__)("Show Topic","amateur-tv"),checked:!!v,onChange:e=>{m({displayTopic:!v})}}),(0,a.createElement)(r.Flex,null,(0,a.createElement)(r.FlexBlock,null,(0,a.createElement)(r.FlexItem,null,(0,a.createElement)(r.TextControl,{label:(0,t.__)("Link","amateur-tv"),value:C,onChange:e=>{m({link:e})},help:(0,t.__)("Absolute or relative URL. Leave blank to use the link of the cam. Placeholders supported: {camname}, {affiliate}","amateur-tv")})),(0,a.createElement)(r.FlexItem,null,(0,a.createElement)(r.ToggleControl,{label:(0,t.__)("Open in new tab","amateur-tv"),checked:!!w,onChange:e=>{m({targetNew:e})}})))),(0,a.createElement)(l.PanelColorSettings,{title:(0,t.__)("Color Settings","amateur-tv"),initialOpen:!1,colorSettings:[{value:i,onChange:e=>{m({usernameColor:e})},label:(0,t.__)("Username/Gender","amateur-tv"),enableAlpha:!0},{value:s,onChange:e=>{m({liveColor:e})},label:(0,t.__)("Live Label","amateur-tv"),enableAlpha:!0},{value:b,onChange:e=>{m({topicColor:e})},label:(0,t.__)("Topic","amateur-tv"),enableAlpha:!0},{value:h,onChange:e=>{m({bgColor:e})},label:(0,t.__)("Background","amateur-tv"),enableAlpha:!0},{value:f,onChange:e=>{m({labelBgColor:e})},label:(0,t.__)("Label Background","amateur-tv"),enableAlpha:!0},{value:P,onChange:e=>{m({textShadowColor:e}),m(void 0!==e?{textShadowValue:[O,e].join(" ")}:{textShadowValue:null})},label:(0,t.__)("Text Shadow","amateur-tv"),enableAlpha:!1}]}),(0,a.createElement)(r.RangeControl,{label:(0,t.__)("Column Gap","amateur-tv"),value:S,initialPosition:3,onChange:e=>{m({columnGap:e})},min:0,max:10}),(0,a.createElement)(r.RangeControl,{label:(0,t.__)("Image Height","amateur-tv"),value:y,initialPosition:115,onChange:e=>{m({imageHeight:e})},min:115,max:500}),(0,a.createElement)(r.RangeControl,{label:(0,t.__)("Image Width","amateur-tv"),value:E,initialPosition:216,onChange:e=>{m({imageWidth:e})},min:216,max:500}),(0,a.createElement)(r.RangeControl,{label:(0,t.__)("Auto Refresh (minutes)","amateur-tv"),value:k,initialPosition:0,onChange:e=>{m({autoRefresh:e})},min:0,max:10}))),!!I&&(0,a.createElement)("div",{key:"loading",className:"wp-block-embed is-loading"},(0,a.createElement)(r.Spinner,null),(0,a.createElement)("p",null,(0,t.__)("Fetching...","amateur-tv"))),(0,a.createElement)("div",(0,l.useBlockProps)(),(0,a.createElement)("div",{className:"atv-cams-list",style:{backgroundColor:h,gap:S}},!!R&&R.slice(0,T>0?T:R.length).map(((e,l)=>(0,a.createElement)("a",{key:l,target:"_blank",className:"atv-cam"},(0,a.createElement)("img",{src:e.image,width:E,height:y,style:{maxHeight:y}}),(0,a.createElement)("div",{className:"atv-annotations"},!!c&&(0,a.createElement)("span",{className:"atv-live atv-padding",style:{color:s,backgroundColor:f,textShadow:N}},(0,t.__)("Live","amateur-tv")),!!g&&(0,a.createElement)("span",{className:"atv-genre atv-padding",style:{color:i,backgroundColor:f,textShadow:N}},(0,t.__)(e.genre,"amateur-tv")),!!p&&(0,a.createElement)("span",{className:"atv-viewers atv-padding",style:{color:s,backgroundColor:f,textShadow:N}},(0,a.createElement)("span",{className:"dashicons dashicons-visibility"}),(0,a.createElement)("span",null,e.viewers)),(0,a.createElement)("span",{className:"atv-username atv-padding",style:{color:i,backgroundColor:f,textShadow:N}},e.username),!!v&&(0,a.createElement)("div",{className:"atv-topic atv-padding",style:{color:b,backgroundColor:f,textShadow:N}},e.topic[u||"en"]))))))))},save:function(e){return null}}),(0,e.registerBlockType)("amateur-tv/iframe",{edit:function(e){(0,l.useBlockProps)();const{attributes:n,setAttributes:o}=e,[m,i]=(0,a.useState)(!1),{genre:u,age:s,iframeHeight:c,camType:v,camName:g}=n,p={popular:(0,t.__)("It will randomly show a live cam from the most popular cams according to your filters","amateur-tv"),camname:(0,t.__)("It will show the cam of the below mentioned username, even if it is offline. If the name doesn't exist, it will show a random cam from the same genre","amateur-tv"),camparam:(0,t.__)('It will show the cam from the parameter on the URL with the name "livecam". If the name doesn\'t exist, it will show a random cam from the same genre',"amateur-tv")},[h,_]=(0,a.useState)(new URL("https://www.amateur.tv/freecam/embed?width=890&height="+c+"&lazyloadvideo=1&a_mute=1"));let d='<iframe width="100%" height="'+c+'" src='+h.toString()+' frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"><\/script>';const[b,C]=(0,a.useState)(d),w=()=>{C('<iframe width="100%" height="'+c+'" src='+h.toString()+' frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"><\/script>')},f=e=>{let a=h,t=e.val;e.multiple&&(t=t.join(",")),t?a.searchParams.set(e.name,t):a.searchParams.delete(e.name),_(h),w()};return(0,a.createElement)(a.Fragment,null,(0,a.createElement)(l.InspectorControls,null,(0,a.createElement)(r.PanelBody,{title:(0,t.__)("Filters","amateur-tv"),initialOpen:!0},(0,a.createElement)(r.SelectControl,{label:(0,t.__)("Gender","amateur-tv"),value:u,multiple:!0,options:[{label:(0,t.__)("Woman","amateur-tv"),value:"W"},{label:(0,t.__)("Couple","amateur-tv"),value:"C"},{label:(0,t.__)("Man","amateur-tv"),value:"M"},{label:(0,t.__)("Trans","amateur-tv"),value:"T"}],onChange:e=>{o({genre:e}),f({name:"genre",val:e,multiple:!0})}}),(0,a.createElement)(r.SelectControl,{label:(0,t.__)("Age","amateur-tv"),value:s,multiple:!0,options:[{label:"18-22",value:"18-22"},{label:"23-29",value:"23-29"},{label:"29-39",value:"29-39"},{label:"40",value:"40"}],onChange:e=>{o({age:e}),f({name:"age",val:e,multiple:!0})}}),(0,a.createElement)(r.RangeControl,{label:(0,t.__)("Iframe Height","amateur-tv"),value:c,initialPosition:590,onChange:e=>{o({iframeHeight:e}),w()},min:200,max:1e3,step:50,type:"stepper",allowReset:!0}),(0,a.createElement)(r.SelectControl,{label:(0,t.__)("Cam Type","amateur-tv"),value:v,options:[{label:(0,t.__)("Most Popular","amateur-tv"),value:"popular"},{label:(0,t.__)("Specific Camname","amateur-tv"),value:"camname"},{label:(0,t.__)("Camname Parameter","amateur-tv"),value:"camparam"}],help:p[v],onChange:e=>{o({camType:e}),f({name:"livecam",val:""})}}),"camname"===v&&(0,a.createElement)(r.TextControl,{label:(0,t.__)("Camname","amateur-tv"),value:g,onChange:e=>{o({camName:e}),f({name:"livecam",val:e})}}))),!!m&&(0,a.createElement)("div",{key:"loading",className:"wp-block-embed is-loading"},(0,a.createElement)(r.Spinner,null),(0,a.createElement)("p",null,(0,t.__)("Fetching...","amateur-tv"))),(0,a.createElement)("div",(0,l.useBlockProps)(),(0,a.createElement)(a.RawHTML,{className:"atv-iframe"},b)))},save:function(e){return null}})}},t={};function l(e){var n=t[e];if(void 0!==n)return n.exports;var r=t[e]={exports:{}};return a[e](r,r.exports,l),r.exports}l.m=a,e=[],l.O=function(a,t,n,r){if(!t){var o=1/0;for(s=0;s<e.length;s++){t=e[s][0],n=e[s][1],r=e[s][2];for(var m=!0,i=0;i<t.length;i++)(!1&r||o>=r)&&Object.keys(l.O).every((function(e){return l.O[e](t[i])}))?t.splice(i--,1):(m=!1,r<o&&(o=r));if(m){e.splice(s--,1);var u=n();void 0!==u&&(a=u)}}return a}r=r||0;for(var s=e.length;s>0&&e[s-1][2]>r;s--)e[s]=e[s-1];e[s]=[t,n,r]},l.o=function(e,a){return Object.prototype.hasOwnProperty.call(e,a)},function(){var e={826:0,431:0};l.O.j=function(a){return 0===e[a]};var a=function(a,t){var n,r,o=t[0],m=t[1],i=t[2],u=0;if(o.some((function(a){return 0!==e[a]}))){for(n in m)l.o(m,n)&&(l.m[n]=m[n]);if(i)var s=i(l)}for(a&&a(t);u<o.length;u++)r=o[u],l.o(e,r)&&e[r]&&e[r][0](),e[r]=0;return l.O(s)},t=self.webpackChunkamateur_tv=self.webpackChunkamateur_tv||[];t.forEach(a.bind(null,0)),t.push=a.bind(null,t.push.bind(t))}();var n=l.O(void 0,[431],(function(){return l(237)}));n=l.O(n)}();