(function(e){function t(t){for(var a,i,s=t[0],c=t[1],u=t[2],d=0,p=[];d<s.length;d++)i=s[d],Object.prototype.hasOwnProperty.call(n,i)&&n[i]&&p.push(n[i][0]),n[i]=0;for(a in c)Object.prototype.hasOwnProperty.call(c,a)&&(e[a]=c[a]);l&&l(t);while(p.length)p.shift()();return o.push.apply(o,u||[]),r()}function r(){for(var e,t=0;t<o.length;t++){for(var r=o[t],a=!0,s=1;s<r.length;s++){var c=r[s];0!==n[c]&&(a=!1)}a&&(o.splice(t--,1),e=i(i.s=r[0]))}return e}var a={},n={app:0},o=[];function i(t){if(a[t])return a[t].exports;var r=a[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.m=e,i.c=a,i.d=function(e,t,r){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},i.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(i.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)i.d(r,a,function(t){return e[t]}.bind(null,a));return r},i.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="wordpress/wp-content/themes/cah-spa/audition-forms/theatre-audition-form/dist/";var s=window["webpackJsonp"]=window["webpackJsonp"]||[],c=s.push.bind(s);s.push=t,s=s.slice();for(var u=0;u<s.length;u++)t(s[u]);var l=c;o.push([0,"chunk-vendors"]),r()})({0:function(e,t,r){e.exports=r("56d7")},"56d7":function(e,t,r){"use strict";r.r(t);r("e260"),r("e6cf"),r("cca6"),r("a79d");var a=r("2b0e"),n=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"container",attrs:{id:"app"}},[r("div",{staticClass:"buttons d-flex flex-row justify-content-between flex-wrap w-100 mb-3"},e._l(e.degreeList,(function(t,a){return r("div",{key:a},[r("button",{staticClass:"btn btn-primary btn-sm mb-2",class:{active:Object.keys(e.selected).length>0&&e.selected.abbr==t.abbr},attrs:{type:"button"},on:{click:function(r){return r.preventDefault(),e.changeReqs(t.abbr)}}},[e._v(e._s(t.name))])])})),0),e.selected&&Object.keys(e.selected).length>0?r("div",{attrs:{id:"reqInfo"}},[r("h2",{staticClass:"heading-underline"},[e._v("Program Requirements for "+e._s(e.selected.name))]),"theatre"===e.specialty?r("div",[r("a",{staticClass:"btn btn-primary mb-3",attrs:{href:"https://performingarts.cah.ucf.edu/audition-theatre",target:"_blank",rel:"noopener"}},[e._v("Apply Now")]),r("div",{attrs:{id:"preamble"}},[e.programCoordinator?r("p",[r("strong",[e._v("Program Coordinator: "+e._s(e.programCoordinator.name))]),e._v(" "),r("a",{attrs:{href:"mailto:"+e.programCoordinator.email}},[e._v(e._s(e.programCoordinator.email))])]):e._e(),"undergrad"===e.selected.level?r("p",{attrs:{id:"undergrad-blurb"}},[e._v(" Students must apply and be accepted through both the UCF School of Performing Arts and "),r("a",{attrs:{href:"https://www.ucf.edu/admissions/undergraduate"}},[e._v("UCF Undergraduate Admissions")]),e._v(". It is encouraged to apply to both UCF Undergraduate Admissions and the UCF School of Performing Arts as early as possible. The school can provisionally accept students prior to being accepted by UCF Undergraduate Admissions but cannot offer official acceptance of study until students receive their UCF acceptance.")]):e._e()]),r("div",{staticClass:"mb-3",domProps:{innerHTML:e._s(e.programCopy)}}),e._m(0)]):"music"===e.specialty?r("div",[r("a",{staticClass:"btn btn-primary mb-3",attrs:{href:"https://performingarts.cah.ucf.edu/audition-music",target:"_blank",rel:"noopener"}},[e._v("Apply Now")]),r("div",{staticClass:"mb-3",attrs:{id:"programReqs"},domProps:{innerHTML:e._s(e.programCopy)}})]):e._e()]):e._e()])},o=[function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{attrs:{id:"postamble"}},[r("p",[r("em",[e._v("We don't want application fees to be a barrier. If you are submitting an application through AcceptD and need assistance, contact the coordinator for the program you are applying to for more information. For information about application fee waivers for UCF admissions, "),r("a",{attrs:{href:"https://www.ucf.edu/admissions/undergraduate/question/how-can-i-request-a-waiver-of-the-application-fee/",target:"_blank",rel:"noopener"}},[e._v("click here")]),e._v(".")])])])}],i=(r("99af"),r("a15b"),r("4fad"),r("2909")),s=r("3835"),c=r("b85c"),u=r("5530"),l=r("2f62"),d={name:"App",data:function(){return{degreeLookup:{music:{undergrad:{"ba-music":"Bachelor of Arts, Music","bm-performance":"Bachelor of Music (Performance Track)","bm-jazz":"Bachelor of Music (Jazz Track)","bm-composition":"Bachelor of Music (Composition Track)",bme:"Bachelor of Music Education"},grad:{"ma-music":"Master of Arts, Music Studies","ma-conducting":"Master of Arts, Conducting"}},theatre:{undergrad:{"ba-theatre":"B.A. Theatre","bfa-acting":"BFA Acting","bfa-design-tech":"BFA Design & Technology","bfa-musical-theatre":"BFA Musical Theatre","bfa-stage-mgmt":"BFA Stage Management"},grad:{"ma-theatre":"M.A. Theatre","ma-music-theatre":"M.A. Musical Theatre","mfa-acting":"MFA Acting","mfa-young-theatre":"MFA Theatre for Young Audiences","mfa-themed-exp":"MFA Themed Experience"}}},programCoordinators:{undergrad:{"ba-theatre":{name:"Kristina Tollefson",email:"kristina.tollefson@ucf.edu"},"bfa-acting":{name:"Be Boyd",email:"belinda.boyd@ucf.edu"},"bfa-design-tech":{name:"Bert Scott",email:"bert.scott@ucf.edu"},"bfa-musical-theatre":{name:"Earl D. Weaver",email:"earl.weaver@ucf.edu"},"bfa-stage-mgmt":{name:"Claudia Lynch",email:"claudia.lynch@ucf.edu"}},grad:{"ma-theatre":{name:"Julia Listengarten",email:"julia.listengarten@ucf.edu"},"ma-music-theatre":{name:"Earl D. Weaver",email:"earl.weaver@ucf.edu"},"mfa-acting":{name:"Michael Wainstein",email:"michael.wainstein@ucf.edu"},"mfa-young-theatre":{name:"Vandy Wood",email:"vandy.wood@ucf.edu"},"mfa-themed-exp":{name:"Peter Weishar",email:"peter.weishar@ucf.edu"}}},selected:{}}},computed:Object(u["a"])({degreeList:function(){var e=Object.entries(this.degreeLookup[this.specialty]),t=[];if(void 0!==e&&e.length>0){var r,a=Object(c["a"])(e);try{for(a.s();!(r=a.n()).done;)for(var n=Object(s["a"])(r.value,2),o=n[0],i=n[1],u=0,l=Object.entries(i);u<l.length;u++){var d=Object(s["a"])(l[u],2),p=d[0],m=d[1],f={abbr:p,name:m,level:o};t.push(f)}}catch(h){a.e(h)}finally{a.f()}}return t},programCopy:function(){var e=this.programReqs[this.specialty][this.selected.level],t="",r=[];if(void 0!==e){if("music"===this.specialty){var a=e.text,n=e[this.selected.abbr];r=void 0!==n?[].concat(Object(i["a"])(a),Object(i["a"])(n)):a}else"theatre"===this.specialty&&(r=e[this.selected.abbr]);t=r.join("\n")}return t},programCoordinator:function(){var e=this.programCoordinators[this.selected.level][this.selected.abbr];return void 0!==e&&e}},Object(l["c"])(["specialty","programReqs"])),methods:Object(u["a"])({changeReqs:function(e){var t,r=Object(c["a"])(this.degreeList);try{for(r.s();!(t=r.n()).done;){var a=t.value;if(e===a.abbr){this.selected=a;break}}}catch(n){r.e(n)}finally{r.f()}}},Object(l["b"])(["init"])),created:function(){this.init()}},p=d,m=(r("5c0b"),r("2877")),f=Object(m["a"])(p,n,o,!1,null,null,null),h=f.exports,g=(r("96cf"),r("1da1")),b=r("bc3a"),v=r.n(b);a["a"].use(l["a"]);var y=new l["a"].Store({state:{programReqs:{},programReqsUri:wpVars.programReqsUri,specialty:wpVars.spec},getters:{},mutations:{updateSpec:function(e,t){a["a"].set(e,"specialty",t)},updateProgramReqs:function(e,t){var r=t.spec,n=t.reqs,o=e.programReqs;o[r]=n,a["a"].set(e,"programReqs",o)}},actions:{init:function(e){return Object(g["a"])(regeneratorRuntime.mark((function t(){var r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:r=e.dispatch,r("getSpec").then((function(){r("getProgramReqs")}));case 2:case"end":return t.stop()}}),t)})))()},getSpec:function(e){return Object(g["a"])(regeneratorRuntime.mark((function t(){var r,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:r=e.commit,a=document.querySelector("#spec").value,r("updateSpec",a);case 3:case"end":return t.stop()}}),t)})))()},getProgramReqs:function(e){return Object(g["a"])(regeneratorRuntime.mark((function t(){var r,a,n,o,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return r=e.commit,a=e.state,n="".concat(a.programReqsUri,"/").concat(a.specialty,"-program-reqs.json"),t.next=4,v.a.get(n).then((function(e){return e.data}));case 4:o=t.sent,i={spec:a.specialty,reqs:o},r("updateProgramReqs",i);case 7:case"end":return t.stop()}}),t)})))()}},modules:{}});a["a"].config.productionTip=!1,new a["a"]({store:y,render:function(e){return e(h)}}).$mount("#program-reqs-app")},"5c0b":function(e,t,r){"use strict";r("9c0c")},"9c0c":function(e,t,r){}});