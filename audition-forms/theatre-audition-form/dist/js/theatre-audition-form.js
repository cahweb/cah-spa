(function(e){function t(t){for(var s,r,l=t[0],o=t[1],u=t[2],d=0,m=[];d<l.length;d++)r=l[d],Object.prototype.hasOwnProperty.call(n,r)&&n[r]&&m.push(n[r][0]),n[r]=0;for(s in o)Object.prototype.hasOwnProperty.call(o,s)&&(e[s]=o[s]);c&&c(t);while(m.length)m.shift()();return i.push.apply(i,u||[]),a()}function a(){for(var e,t=0;t<i.length;t++){for(var a=i[t],s=!0,l=1;l<a.length;l++){var o=a[l];0!==n[o]&&(s=!1)}s&&(i.splice(t--,1),e=r(r.s=a[0]))}return e}var s={},n={app:0},i=[];function r(t){if(s[t])return s[t].exports;var a=s[t]={i:t,l:!1,exports:{}};return e[t].call(a.exports,a,a.exports,r),a.l=!0,a.exports}r.m=e,r.c=s,r.d=function(e,t,a){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:a})},r.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(r.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var s in e)r.d(a,s,function(t){return e[t]}.bind(null,s));return a},r.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="wordpress/wp-content/themes/cah-spa/audition-forms/theatre-audition-form/dist/";var l=window["webpackJsonp"]=window["webpackJsonp"]||[],o=l.push.bind(l);l.push=t,l=l.slice();for(var u=0;u<l.length;u++)t(l[u]);var c=o;i.push([0,"chunk-vendors"]),a()})({0:function(e,t,a){e.exports=a("56d7")},"009f":function(e,t,a){"use strict";var s=a("9bc6"),n=a.n(s);n.a},"0c64":function(e,t,a){},"23d5":function(e,t,a){"use strict";var s=a("0c64"),n=a.n(s);n.a},"56d7":function(e,t,a){"use strict";a.r(t);a("e260"),a("e6cf"),a("cca6"),a("a79d");var s=a("2b0e"),n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"app"},[e.isSubmitted?a("div",{staticClass:"status-container w-100 rounded",staticStyle:{"min-height":"100px"}},[a("p",{staticClass:"text-center",attrs:{id:"status-msg"}},[e._v(e._s(e.statusMessage))]),e.isProcessing?a("progress-bar",{attrs:{value:e.uploadPercentage}}):e._e()],1):a("div",{staticClass:"form-container"},[a("form",{ref:"form",attrs:{id:"theatre-audition-form",method:"post",enctype:"multipart/form-data"}},[a("p",[e._v("Are you auditioning for an Undergraduate or Graduate program?")]),a("div",{staticClass:"radio-group mb-3"},[e._l(e.buttons.level,(function(t,s){return[a("div",{key:s,staticClass:"form-check form-check-inline mx-2"},[a("label",{staticClass:"form-check-label btn btn-primary btn-sm"},[a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.level,expression:"values.level"}],staticClass:"form-check-input",attrs:{type:"radio",id:"level-"+s,name:"level"},domProps:{value:t.value,checked:e._q(e.values.level,t.value)},on:{click:function(t){return e.updateActive("level")},change:function(a){return e.$set(e.values,"level",t.value)}}}),e._v(" "+e._s(t.label)+" ")])])]}))],2),""!==e.values.level?a("div",{staticClass:"mb-5"},[a("p",[e._v("Which program were you interested in?")]),a("div",{staticClass:"radio-group mb-3"},[e._l("undergrad"==e.values.level?e.buttons.programs.undergrad:e.buttons.programs.grad,(function(t,s){return[a("div",{key:s,staticClass:"form-check form-check-inline mx-2"},[a("label",{staticClass:"form-check-label btn btn-primary btn-sm"},[a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.program,expression:"values.program"}],staticClass:"form-check-input",attrs:{type:"radio",id:"program-"+s,name:"program"},domProps:{value:t,checked:e._q(e.values.program,t)},on:{click:function(t){return e.updateActive("program")},change:function(a){return e.$set(e.values,"program",t)}}}),e._v(" "+e._s(t.label)+" ")])])]}))],2)]):e._e(),e.showReqs?a("div",{staticClass:"mb-5"},[a("h2",{staticClass:"heading-underline"},[e._v("Program Requirements for "+e._s(e.values.program.label))]),a("div",{domProps:{innerHTML:e._s(e.programReqs)}})]):e._e(),e.isBfaActing?a("div",{staticClass:"mb-3"},[a("p",{staticClass:"mt-4"},[e._v("Did you want to schedule a live audition or submit a video audition?")]),a("div",{staticClass:"radio-group"},[e._l(e.buttons.bfaActingChoice,(function(t,s){return[a("div",{key:s,staticClass:"form-check form-check-inline mx-2"},[a("label",{staticClass:"form-check-label btn btn-primary btn-sm"},[a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.actingChoice,expression:"values.actingChoice"}],staticClass:"form-check-input",attrs:{type:"radio",id:"bfa-acting-choice-"+s,name:"actingChoice"},domProps:{value:t.value,checked:e._q(e.values.actingChoice,t.value)},on:{click:function(t){return e.updateActive("actingChoice")},change:function(a){return e.$set(e.values,"actingChoice",t.value)}}}),e._v(" "+e._s(t.label)+" ")])])]}))],2)]):e._e(),e.showForm?a("div",{staticClass:"mb-3"},[a("h4",[e._v("Student Information")]),a("div",{staticClass:"row w-75"},[e._l(e.inputs.studentInfo,(function(t,s){return[a("div",{key:s,staticClass:"form-group col-md-6"},[a("label",[e._v(e._s(t.label)+":")]),"checkbox"===(void 0!==t.type?t.type:"text")?a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.studentInfo[t.name],expression:"values.studentInfo[input.name]"}],staticClass:"form-control",attrs:{name:t.name,maxlength:void 0!==t.maxlength?t.maxlength:null,required:t.required,type:"checkbox"},domProps:{checked:Array.isArray(e.values.studentInfo[t.name])?e._i(e.values.studentInfo[t.name],null)>-1:e.values.studentInfo[t.name]},on:{change:function(a){var s=e.values.studentInfo[t.name],n=a.target,i=!!n.checked;if(Array.isArray(s)){var r=null,l=e._i(s,r);n.checked?l<0&&e.$set(e.values.studentInfo,t.name,s.concat([r])):l>-1&&e.$set(e.values.studentInfo,t.name,s.slice(0,l).concat(s.slice(l+1)))}else e.$set(e.values.studentInfo,t.name,i)}}}):"radio"===(void 0!==t.type?t.type:"text")?a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.studentInfo[t.name],expression:"values.studentInfo[input.name]"}],staticClass:"form-control",attrs:{name:t.name,maxlength:void 0!==t.maxlength?t.maxlength:null,required:t.required,type:"radio"},domProps:{checked:e._q(e.values.studentInfo[t.name],null)},on:{change:function(a){return e.$set(e.values.studentInfo,t.name,null)}}}):a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.studentInfo[t.name],expression:"values.studentInfo[input.name]"}],staticClass:"form-control",attrs:{name:t.name,maxlength:void 0!==t.maxlength?t.maxlength:null,required:t.required,type:void 0!==t.type?t.type:"text"},domProps:{value:e.values.studentInfo[t.name]},on:{input:function(a){a.target.composing||e.$set(e.values.studentInfo,t.name,a.target.value)}}})])]}))],2),a("div",{staticClass:"row w-75 mb-3"},[e._l(e.inputs.address,(function(t,s){return[a("div",{key:s,staticClass:"form-group",class:"col-md-"+(void 0!==t.colWidth?t.colWidth:6)},[a("label",[e._v(e._s(t.label)+":")]),"checkbox"===(void 0!==t.type?t.type:"text")?a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.studentInfo.address[t.name],expression:"values.studentInfo.address[input.name]"}],staticClass:"form-control",attrs:{name:t.name,maxlength:void 0!==t.maxlength?t.maxlength:null,type:"checkbox"},domProps:{checked:Array.isArray(e.values.studentInfo.address[t.name])?e._i(e.values.studentInfo.address[t.name],null)>-1:e.values.studentInfo.address[t.name]},on:{change:function(a){var s=e.values.studentInfo.address[t.name],n=a.target,i=!!n.checked;if(Array.isArray(s)){var r=null,l=e._i(s,r);n.checked?l<0&&e.$set(e.values.studentInfo.address,t.name,s.concat([r])):l>-1&&e.$set(e.values.studentInfo.address,t.name,s.slice(0,l).concat(s.slice(l+1)))}else e.$set(e.values.studentInfo.address,t.name,i)}}}):"radio"===(void 0!==t.type?t.type:"text")?a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.studentInfo.address[t.name],expression:"values.studentInfo.address[input.name]"}],staticClass:"form-control",attrs:{name:t.name,maxlength:void 0!==t.maxlength?t.maxlength:null,type:"radio"},domProps:{checked:e._q(e.values.studentInfo.address[t.name],null)},on:{change:function(a){return e.$set(e.values.studentInfo.address,t.name,null)}}}):a("input",{directives:[{name:"model",rawName:"v-model",value:e.values.studentInfo.address[t.name],expression:"values.studentInfo.address[input.name]"}],staticClass:"form-control",attrs:{name:t.name,maxlength:void 0!==t.maxlength?t.maxlength:null,type:void 0!==t.type?t.type:"text"},domProps:{value:e.values.studentInfo.address[t.name]},on:{input:function(a){a.target.composing||e.$set(e.values.studentInfo.address,t.name,a.target.value)}}})])]}))],2),a("h4",[e._v("Audition Information")]),a("div",{staticClass:"row w-75 mb-3"},[e._l(["First","Second"],(function(t,s){return[a("div",{key:s,staticClass:"form-group col-md-7",class:{"has-danger":e.sameDate}},[a("label",[e._v(e._s(t)+" Choice Date:")]),a("select",{directives:[{name:"model",rawName:"v-model",value:e.values.auditionDates[t.toLowerCase()],expression:"values.auditionDates[ordinal.toLowerCase()]"}],staticClass:"form-control",class:{"form-control-danger":e.sameDate},attrs:{name:"audition"+(s+1)},on:{change:function(a){var s=Array.prototype.filter.call(a.target.options,(function(e){return e.selected})).map((function(e){var t="_value"in e?e._value:e.value;return t}));e.$set(e.values.auditionDates,t.toLowerCase(),a.target.multiple?s:s[0])}}},[a("option",{attrs:{value:""}},[e._v("-- Please Select --")]),e._l(e.dateList,(function(t,s){return a("option",{key:s,domProps:{value:t.value}},[e._v(e._s(t.text))])}))],2),"Second"===t&&e.sameDate?a("div",{staticClass:"form-control-feedback"},[e._v("Can't select the same date.")]):e._e()])]}))],2),a("h5",[e._v("Additional Documents")]),a("div",{staticClass:"row w-75"},[a("p",{staticClass:"col-12"},[e._v("Résumé is required. Maximum 10 files or 10 MB total.")]),a("div",{staticClass:"col-md-7"},[a("file-input",{attrs:{name:"resume",file:e.values.files.resume,acceptedFileTypes:e.acceptedFileTypes,index:-1,required:!0},on:{fileChange:function(t){e.fileChange(t)},removeFile:function(t){e.removeFile(t)}}})],1),e.values.files.extra.length>0?a("div",{staticClass:"col-md-7"},e._l(e.extraFileList,(function(t,s){return a("file-input",{key:s,attrs:{index:s,required:!1,name:"file-extra-"+s,file:t,acceptedFileTypes:e.acceptedFileTypes},on:{fileChange:function(t){e.fileChange(t)},removeFile:function(t){e.removeFile(t)}}})})),1):e._e()]),a("div",{staticClass:"row w-75"},[a("div",{staticClass:"col-md-7"},[a("button",{staticClass:"btn btn-complementary btn-sm mt-2 rounded float-right",attrs:{type:"button"},on:{click:e.newFile}},[e._v("+")])])]),a("button",{staticClass:"btn btn-secondary",attrs:{type:"submit"},on:{click:e.submitForm}},[e._v("Submit")])]):e._e(),e.showAcceptd?a("div",{staticClass:"mb-3"},[a("p",{domProps:{innerHTML:e._s(e.acceptdMessage)}})]):e._e()])])])},i=[],r=(a("99af"),a("4de4"),a("4160"),a("caad"),a("a15b"),a("d81d"),a("13d5"),a("b0c0"),a("4fad"),a("d3b7"),a("2532"),a("159b"),a("ddb0"),a("3835")),l=a("b85c"),o=a("bc3a"),u=a.n(o),c=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"form-group"},["resume"===e.name?a("label",[e._v("Résumé")]):e._e(),a("input",{ref:"input",staticClass:"form-control",attrs:{type:"file",name:e.name,accept:e.acceptedFileTypes},on:{change:e.fileChange}}),a("small",{staticClass:"form-text text-right",attrs:{required:e.required}},[a("a",{staticClass:"text-danger",attrs:{href:"#"},on:{click:e.removeFile}},[e._v("(Remove)")])])])},d=[],m=(a("a9e3"),{props:{name:String,acceptedFileTypes:String,index:Number,file:File,required:Boolean},data:function(){return{}},computed:{files:function(){return this.$refs.input.files}},methods:{fileChange:function(){this.files.length>0?this.$emit("fileChange",{name:this.name,index:this.index,file:this.files[0]}):this.removeFile()},removeFile:function(e){if(e.preventDefault(),this.files.length>0){var t=new DataTransfer;this.$refs.input.files=t.files}this.$emit("removeFile",{name:this.name,index:this.index})}},mounted:function(){if(this.file instanceof File){var e=new DataTransfer;e.items.add(this.file),this.$refs.input.files=e.files}}}),v=m,f=(a("6555"),a("2877")),p=Object(f["a"])(v,c,d,!1,null,"4c3cd9c8",null),h=p.exports,g=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"progress"},[a("div",{staticClass:"progress-bar progress-bar-striped progress-bar-animated bg-success",style:"width: "+e.value+"%;",attrs:{role:"progressbar","aria-valuenow":e.value,"aria-valuemin":"0","aria-valuemax":"100"}})])},b=[],x={props:{value:Number},data:function(){return{}}},y=x,C=(a("23d5"),Object(f["a"])(y,g,b,!1,null,"0493ba48",null)),_=C.exports,w={components:{"file-input":h,"progress-bar":_},data:function(){return{values:{level:"",program:"",actingChoice:"",studentInfo:{fname:"",lname:"",email:"",phone:"",address:{street1:"",street2:"",city:"",state:"",zip:""}},auditionDates:{first:"",second:""},files:{resume:null,extra:[]}},buttons:{level:[{label:"Undergraduate",value:"undergrad"},{label:"Graduate",value:"grad"}],programs:{undergrad:[{label:"B.A. Theatre Studies",value:"ba-theatre"},{label:"B.F.A. Acting",value:"bfa-acting"},{label:"B.F.A. Design & Technology",value:"bfa-design-tech"},{label:"B.F.A. Musical Theatre",value:"bfa-musical-theatre"},{label:"B.F.A. Stage Management",value:"bfa-stage-mgmt"}],grad:[{label:"M.A. Theatre Studies",value:"ma-theatre"},{label:"M.A. Theatre, Musical Theatre Concentration",value:"ma-music-theatre"},{label:"M.F.A. Acting",value:"mfa-acting"},{label:"M.F.A. Theatre for Young Audiences",value:"mfa-young-theatre"},{label:"M.F.A. Theatre, Themed Experience",value:"mfa-themed-exp"}]},bfaActingChoice:[{label:"Live Audition",value:"live"},{label:"Submit Video",value:"video"}]},inputs:{studentInfo:[{label:"First Name",name:"fname",required:!0},{label:"Last Name",name:"lname",required:!0},{label:"Email",name:"email",type:"email",required:!0},{label:"Phone",name:"phone",type:"tel",maxlength:10,required:!0}],address:[{label:"Street Address (Line 1)",name:"street1",required:!0},{label:"Street Address (Line 2)",name:"street2",required:!1},{label:"City",name:"city",required:!0},{label:"State",name:"state",colWidth:2,maxlength:2,required:!0},{label:"ZIP Code",name:"zip",type:"number",colWidth:4,maxlength:5,required:!0}]},acceptdPrograms:{undergrad:["bfa-musical-theatre"],grad:["mfa-young-theatre"]},requirementList:{},dateOptions:{},baseUrl:wpVars.baseUrl,ajaxUrl:wpVars.ajaxUrl,nonce:"",isSubmitted:!1,isProcessing:!1,uploadPercentage:0,doneMessage:"",maxUploadSize:10485760}},computed:{selectedProgram:function(){return""!==this.values.program?this.values.program.value:null},showReqs:function(){return""!==this.values.level&&""!==this.values.program&&void 0!==this.requirementList[this.values.level][this.values.program.value]},programReqs:function(){if(""!==this.values.level&&""!==this.values.program){var e=this.requirementList[this.values.level][this.values.program.value];if(void 0!==e&&e.length>0)return e.join("\n")}return"Not found"},isBfaActing:function(){return"undergrad"===this.values.level&&""!==this.values.program&&"bfa-acting"===this.values.program.value},showForm:function(){var e=this.selectedProgram;return"undergrad"===this.values.level&&("ba-theatre"===e||"bfa-design-tech"===e||"bfa-stage-mgmt"===e||"bfa-acting"===e&&"live"===this.values.actingChoice)},dateList:function(){return"ba-theatre"===this.selectedProgram?this.dateOptions[this.selectedProgram]:this.dateOptions.general},sameDate:function(){return""!==this.values.auditionDates.first&&""!==this.values.auditionDates.second&&this.values.auditionDates.first==this.values.auditionDates.second},acceptedFileTypes:function(){return"application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/rtf, image/jpeg, image/png, text/plain"},extraFileList:function(){return this.values.files.extra},showAcceptd:function(){return""!==this.values.program&&(this.isBfaActing&&"video"===this.values.actingChoice||void 0!==this.acceptdPrograms[this.values.level]&&this.acceptdPrograms[this.values.level].includes(this.values.program.value))},acceptdMessage:function(){return'Click the link to continue to <a href="https://app.getacceptd.com/ucftheatre" target="_blank" rel="noopener">Acceptd</a> in order to complete your application.'},isUploading:function(){return this.isProcessing&&this.uploadPercentage<100},isScanning:function(){return this.isProcessing&&100===this.uploadPercentage},statusMessage:function(){return this.isUploading?"Uploading files...":this.isScanning?"Scanning submitted files for viruses...":this.doneMessage},totalFileSize:function(){var e=this.values.files.resume.size,t=this.values.files.extra.length?this.values.files.extra.reduce((function(e,t){return e+(null!==t?t.size:0)})):0;return e+t}},methods:{updateActive:function(e){var t=document.querySelectorAll("input[name=".concat(e,"]"));t.forEach((function(e){e.checked?e.closest("label").classList.add("active"):e.closest("label").classList.remove("active")}))},fileChange:function(e){var t=e.name,a=e.index,s=e.file;if("resume"===t)this.values.files.resume=s;else{var n=this.values.files.extra.map((function(e,t){return t===a?s:e}));this.values.files.extra=n}},removeFile:function(e){var t=e.name,a=e.index;if("resume"===t)this.values.files.resume=null;else{var s=this.values.files.extra.filter((function(e,t){return t!==a}));this.values.files.extra=s}},newFile:function(){this.extraFileList.length<9&&this.totalFileSize<this.maxUploadSize?this.values.files.extra.push(null):alert("Maximum of 10 files allowed, for a maximum total of 10 MB.")},submitForm:function(e){var t=this;e.preventDefault(),e.stopImmediatePropagation(),this.isSubmitted=!0,this.isProcessing=!0;var a,s=this.values.studentInfo,n=s.address,i={lname:s.lname,fname:s.fname,email:s.email,address:"".concat(n.street1,",").concat(""!=n.street2?" "+n.street2+",":""," ").concat(n.city,", ").concat(n.state," ").concat(n.zip),phone:s.phone,level:this.values.level,program:this.values.program.value,firstChoiceDate:this.values.auditionDates.first,secondChoiceDate:this.values.auditionDates.second,resume:this.values.files.resume},o=Object(l["a"])(this.values.files.extra.entries());try{for(o.s();!(a=o.n()).done;){var c=Object(r["a"])(a.value,2),d=c[0],m=c[1];i["extra".concat(d)]=m}}catch(y){o.e(y)}finally{o.f()}var v=new FormData;v.append("action","theatre_form_submit"),v.append("theatre-form-nonce",this.nonce);for(var f=0,p=Object.entries(i);f<p.length;f++){var h=Object(r["a"])(p[f],2),g=h[0],b=h[1];v.append(g,b)}var x={onUploadProgress:function(e){t.uploadPercentage=parseInt(Math.round(e.loaded/e.total*100))}};u.a.post(this.ajaxUrl,v,x).then((function(e){t.doneMessage=e.data})).catch((function(e){console.error(e)})).finally((function(){t.isProcessing=!1}))}},created:function(){var e=this,t="".concat(this.baseUrl,"/dist/json/theatre-program-reqs.json");u.a.get(t).then((function(t){e.requirementList=t.data})),t="".concat(this.baseUrl,"/dist/json/theatre-audition-dates.json"),u.a.get(t).then((function(t){e.dateOptions=t.data}));var a=document.querySelector("input[name=theatre-form-nonce]");this.nonce=a.value,a.remove()}},k=w,I=(a("009f"),Object(f["a"])(k,n,i,!1,null,"3911ad1c",null)),P=I.exports;s["a"].config.productionTip=!1,new s["a"]({render:function(e){return e(P)}}).$mount("#theatre-audition-app")},6555:function(e,t,a){"use strict";var s=a("dc51"),n=a.n(s);n.a},"9bc6":function(e,t,a){},dc51:function(e,t,a){}});