import{az as m,aA as d,aB as u,aC as E,aD as C,aE as g,aF as a}from"./@vue-b9898b5a.js";/**
* vue v3.5.3
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/const f=new WeakMap;function h(e){let n=f.get(e??a);return n||(n=Object.create(null),f.set(e??a,n)),n}function T(e,n){if(!d(e))if(e.nodeType)e=e.innerHTML;else return u;const o=e,t=h(n),s=t[o];if(s)return s;if(e[0]==="#"){const c=document.querySelector(e);e=c?c.innerHTML:""}const r=E({hoistStatic:!0,onError:void 0,onWarn:u},n);!r.isCustomElement&&typeof customElements<"u"&&(r.isCustomElement=c=>!!customElements.get(c));const{code:l}=C(e,r),i=new Function("Vue",l)(g);return i._rc=!0,t[o]=i}m(T);
