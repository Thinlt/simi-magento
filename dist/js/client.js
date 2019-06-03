!function(e){function webpackJsonpCallback(t){for(var i,o,a=t[0],l=t[1],c=t[2],s=0,f=[];s<a.length;s++)o=a[s],n[o]&&f.push(n[o][0]),n[o]=0
for(i in l)Object.prototype.hasOwnProperty.call(l,i)&&(e[i]=l[i])
for(u&&u(t);f.length;)f.shift()()
return r.push.apply(r,c||[]),checkDeferredModules()}function checkDeferredModules(){for(var e,t=0;t<r.length;t++){for(var i=r[t],o=!0,a=1;a<i.length;a++){var u=i[a]
0!==n[u]&&(o=!1)}o&&(r.splice(t--,1),e=__webpack_require__(__webpack_require__.s=i[0]))}return e}var t={},n={0:0},r=[]
function __webpack_require__(n){if(t[n])return t[n].exports
var r=t[n]={i:n,l:!1,exports:{}},i=!0
try{e[n].call(r.exports,r,r.exports,__webpack_require__),i=!1}finally{i&&delete t[n]}return r.l=!0,r.exports}__webpack_require__.m=e,__webpack_require__.c=t,__webpack_require__.d=function(e,t,n){__webpack_require__.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},__webpack_require__.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},__webpack_require__.t=function(e,t){if(1&t&&(e=__webpack_require__(e)),8&t)return e
if(4&t&&"object"==typeof e&&e&&e.__esModule)return e
var n=Object.create(null)
if(__webpack_require__.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)__webpack_require__.d(n,r,function(t){return e[t]}.bind(null,r))
return n},__webpack_require__.n=function(e){var t=e&&e.__esModule?function getDefault(){return e.default}:function getModuleExports(){return e}
return __webpack_require__.d(t,"a",t),t},__webpack_require__.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},__webpack_require__.p="/"
var i=window.webpackJsonp=window.webpackJsonp||[],o=i.push.bind(i)
i.push=webpackJsonpCallback,i=i.slice()
for(var a=0;a<i.length;a++)webpackJsonpCallback(i[a])
var u=o
r.push([23,1]),checkDeferredModules()}({20:function(e,t,n){"use strict"
e.exports=n(21)},21:function(e,n,r){"use strict";(function(e){Object.defineProperty(n,"__esModule",{value:!0})
var r=null,i=!1,o=3,a=-1,l=-1,c=!1,s=!1
function p(){if(!c){var e=r.expirationTime
s?k():s=!0,g(t,e)}}function u(){var e=r,t=r.next
if(r===t)r=null
else{var n=r.previous
r=n.next=t,t.previous=n}e.next=e.previous=null,n=e.callback,t=e.expirationTime,e=e.priorityLevel
var i=o,a=l
o=e,l=t
try{var u=n()}finally{o=i,l=a}if("function"==typeof u)if(u={callback:u,priorityLevel:e,expirationTime:t,next:null,previous:null},null===r)r=u.next=u.previous=u
else{n=null,e=r
do{if(e.expirationTime>=t){n=e
break}e=e.next}while(e!==r)
null===n?n=r:n===r&&(r=u,p()),(t=n.previous).next=n.previous=u,u.next=n,u.previous=t}}function v(){if(-1===a&&null!==r&&1===r.priorityLevel){c=!0
try{do{u()}while(null!==r&&1===r.priorityLevel)}finally{c=!1,null!==r?p():s=!1}}}function t(e){c=!0
var t=i
i=e
try{if(e)for(;null!==r;){var o=n.unstable_now()
if(!(r.expirationTime<=o))break
do{u()}while(null!==r&&r.expirationTime<=o)}else if(null!==r)do{u()}while(null!==r&&!x())}finally{c=!1,i=t,null!==r?p():s=!1,v()}}var f,_,b=Date,d="function"==typeof setTimeout?setTimeout:void 0,w="function"==typeof clearTimeout?clearTimeout:void 0,y="function"==typeof requestAnimationFrame?requestAnimationFrame:void 0,m="function"==typeof cancelAnimationFrame?cancelAnimationFrame:void 0
function E(e){f=y(function(t){w(_),e(t)}),_=d(function(){m(f),e(n.unstable_now())},100)}if("object"==typeof performance&&"function"==typeof performance.now){var h=performance
n.unstable_now=function(){return h.now()}}else n.unstable_now=function(){return b.now()}
var g,k,x,O=null
if("undefined"!=typeof window?O=window:void 0!==e&&(O=e),O&&O._schedMock){var j=O._schedMock
g=j[0],k=j[1],x=j[2],n.unstable_now=j[3]}else if("undefined"==typeof window||"function"!=typeof MessageChannel){var P=null,S=function(e){if(null!==P)try{P(e)}finally{P=null}}
g=function(e){null!==P?setTimeout(g,0,e):(P=e,setTimeout(S,0,!1))},k=function(){P=null},x=function(){return!1}}else{var q=null,I=!1,T=-1,M=!1,C=!1,L=0,N=33,J=33
x=function(){return L<=n.unstable_now()}
var B=new MessageChannel,D=B.port2
B.port1.onmessage=function(){I=!1
var e=q,t=T
q=null,T=-1
var r=n.unstable_now(),i=!1
if(0>=L-r){if(!(-1!==t&&t<=r))return M||(M=!0,E(K)),q=e,void(T=t)
i=!0}if(null!==e){C=!0
try{e(i)}finally{C=!1}}}
var K=function(e){if(null!==q){E(K)
var t=e-L+J
t<J&&N<J?(8>t&&(t=8),J=t<N?N:t):N=t,L=e+J,I||(I=!0,D.postMessage(void 0))}else M=!1}
g=function(e,t){q=e,T=t,C||0>t?D.postMessage(void 0):M||(M=!0,E(K))},k=function(){q=null,I=!1,T=-1}}n.unstable_ImmediatePriority=1,n.unstable_UserBlockingPriority=2,n.unstable_NormalPriority=3,n.unstable_IdlePriority=5,n.unstable_LowPriority=4,n.unstable_runWithPriority=function(e,t){switch(e){case 1:case 2:case 3:case 4:case 5:break
default:e=3}var r=o,i=a
o=e,a=n.unstable_now()
try{return t()}finally{o=r,a=i,v()}},n.unstable_next=function(e){switch(o){case 1:case 2:case 3:var t=3
break
default:t=o}var r=o,i=a
o=t,a=n.unstable_now()
try{return e()}finally{o=r,a=i,v()}},n.unstable_scheduleCallback=function(e,t){var i=-1!==a?a:n.unstable_now()
if("object"==typeof t&&null!==t&&"number"==typeof t.timeout)t=i+t.timeout
else switch(o){case 1:t=i+-1
break
case 2:t=i+250
break
case 5:t=i+1073741823
break
case 4:t=i+1e4
break
default:t=i+5e3}if(e={callback:e,priorityLevel:o,expirationTime:t,next:null,previous:null},null===r)r=e.next=e.previous=e,p()
else{i=null
var u=r
do{if(u.expirationTime>t){i=u
break}u=u.next}while(u!==r)
null===i?i=r:i===r&&(r=e,p()),(t=i.previous).next=i.previous=e,e.next=i,e.previous=t}return e},n.unstable_cancelCallback=function(e){var t=e.next
if(null!==t){if(t===e)r=null
else{e===r&&(r=t)
var n=e.previous
n.next=t,t.previous=n}e.next=e.previous=null}},n.unstable_wrapCallback=function(e){var t=o
return function(){var r=o,i=a
o=t,a=n.unstable_now()
try{return e.apply(this,arguments)}finally{o=r,a=i,v()}}},n.unstable_getCurrentPriorityLevel=function(){return o},n.unstable_shouldYield=function(){return!i&&(null!==r&&r.expirationTime<l||x())},n.unstable_continueExecution=function(){null!==r&&p()},n.unstable_pauseExecution=function(){},n.unstable_getFirstCallbackNode=function(){return r}}).call(this,r(22))},22:function(e,t){var n
n=function(){return this}()
try{n=n||new Function("return this")()}catch(e){"object"==typeof window&&(n=window)}e.exports=n},23:function(e,t,n){"use strict"
n.r(t)
var r={}
n.r(r),n.d(r,"BrowserPersistence",function(){return BrowserPersistence})
n(2)
var i=n(4),o=n.n(i)
class NamespacedLocalStorage{constructor(e,t){this.localStorage=e,this.key=t}_makeKey(e){return`${this.key}__${e}`}getItem(e){return this.localStorage.getItem(this._makeKey(e))}setItem(e,t){return this.localStorage.setItem(this._makeKey(e),t)}removeItem(e){return this.localStorage.removeItem(this._makeKey(e))}}class BrowserPersistence{constructor(){this.storage=new NamespacedLocalStorage(window.localStorage,this.constructor.KEY||BrowserPersistence.KEY)}getItem(e){const t=Date.now(),n=this.storage.getItem(e)
if(!n)return
const{value:r,ttl:i,timeStored:o}=JSON.parse(n)
if(!(i&&t-o>1e3*i))return JSON.parse(r)
this.storage.removeItem(e)}setItem(e,t,n){const r=Date.now()
this.storage.setItem(e,JSON.stringify({value:JSON.stringify(t),timeStored:r,ttl:n}))}removeItem(e){this.storage.removeItem(e)}}!function _defineProperty(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}(BrowserPersistence,"KEY","M2_VENIA_BROWSER_PERSISTENCE")
new URL("/graphql",location.origin).toString()
o.a.render("Hello world",document.getElementById("root")),"serviceWorker"in navigator&&window.addEventListener("load",function(){navigator.serviceWorker.register("sw.js").then(function(e){}).catch(function(e){})}),window.addEventListener("online",function(){store.dispatch(app.setOnline())}),window.addEventListener("offline",function(){store.dispatch(app.setOffline())})},3:function(e,t,n){"use strict"
var r=Object.getOwnPropertySymbols,i=Object.prototype.hasOwnProperty,o=Object.prototype.propertyIsEnumerable
e.exports=function shouldUseNative(){try{if(!Object.assign)return!1
var e=new String("abc")
if(e[5]="de","5"===Object.getOwnPropertyNames(e)[0])return!1
for(var t={},n=0;n<10;n++)t["_"+String.fromCharCode(n)]=n
if("0123456789"!==Object.getOwnPropertyNames(t).map(function(e){return t[e]}).join(""))return!1
var r={}
return"abcdefghijklmnopqrst".split("").forEach(function(e){r[e]=e}),"abcdefghijklmnopqrst"===Object.keys(Object.assign({},r)).join("")}catch(e){return!1}}()?Object.assign:function(e,t){for(var n,a,u=function toObject(e){if(null==e)throw new TypeError("Object.assign cannot be called with null or undefined")
return Object(e)}(e),l=1;l<arguments.length;l++){for(var c in n=Object(arguments[l]))i.call(n,c)&&(u[c]=n[c])
if(r){a=r(n)
for(var s=0;s<a.length;s++)o.call(n,a[s])&&(u[a[s]]=n[a[s]])}}return u}}})
