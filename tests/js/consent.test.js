const assert = require('assert');
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync('public/assets/js/consent.js', 'utf8');
function harness({gaId = 'G-TEST123', saved, blockStorage = false} = {}) {
  const events = {}, windowEvents = {}, store = new Map(), nodes = {}, scripts = [];
  const cookies = new Map([['martabucan_session', 'secure-session'], ['XSRF-TOKEN', 'csrf'], ['_ga', 'old'], ['_ga_TEST123', 'old']]);
  let reloads = 0;
  class Element {
    constructor() { this.listeners = {}; this.hidden = false; this.checked = false; this.open = false; this.dataset = {}; this.offsetParent = true; }
    addEventListener(name, callback) { this.listeners[name] = callback; }
    click() { this.listeners.click?.({target:this}); }
    focus() {}
    showModal() { this.open = true; }
    close() { this.open = false; this.listeners.close?.(); }
    querySelector() { return this.child || null; }
    appendChild(child) { this.child = child; child.remove = () => { this.child = null; }; }
  }
  ['#cookie-banner','[data-cookie-launcher]','#cookie-dialog','#maps-consent','#analytics-consent','#map-container','#map-placeholder','#save-consent'].forEach(key => nodes[key] = new Element());
  nodes['#map-container'].dataset.mapSrc = 'https://www.google.com/maps/embed?pb=verified';
  if (!gaId) delete nodes['#analytics-consent'];
  const accept = new Element(), reject = new Element(), settings = new Element(), mapButton = new Element(), close = new Element();
  accept.dataset.consent = 'all'; reject.dataset.consent = 'necessary';
  const groups = {'[data-consent]':[accept,reject], '[data-cookie-settings]':[settings], '[data-close-dialog]':[close], '[data-enable-map]':[mapButton]};
  if (saved) store.set('mb_consent_v2', JSON.stringify(saved));
  const document = {body:{dataset:{ga4Id:gaId}}, head:{appendChild:node=>scripts.push(node)}, hidden:false,
    querySelector:key=>nodes[key] || null, querySelectorAll:key=>groups[key] || [], createElement:()=>new Element(), addEventListener:(name,callback)=>events[name]=callback};
  Object.defineProperty(document, 'cookie', {get:()=>[...cookies].map(([k,v])=>`${k}=${v}`).join('; '), set:value=>{const name=value.split('=')[0];if(value.includes('Max-Age=0')) cookies.delete(name);}});
  const window = {addEventListener:(name,callback)=>windowEvents[name]=callback};
  const location = {hostname:'martabucan.test',protocol:'http:',origin:'http://martabucan.test',pathname:'/',reload:()=>reloads++};
  vm.runInNewContext(source, {document,window,location,Date,Number,JSON,encodeURIComponent,setTimeout:()=>1,clearTimeout:()=>{},localStorage:{getItem:key=>{if(blockStorage) throw Error();return store.get(key) || null;},setItem:(key,value)=>{if(blockStorage) throw Error();store.set(key,value);},removeItem:key=>{if(blockStorage) throw Error();store.delete(key);}}});
  return {nodes,scripts,cookies,window,events,windowEvents,store,accept,reject,settings,mapButton,close,reloads:()=>reloads};
}
const choice = (extra={})=>({version:2,policy:'2026-11-12:G-TEST123',maps:false,analytics:false,timestamp:Date.now(),...extra});
{
 const h=harness();
 assert.equal(h.scripts.length,0); assert.equal(h.nodes['#map-container'].child,null); assert.equal(h.nodes['#cookie-banner'].hidden,false);
 h.reject.click(); assert.equal(h.scripts.length,0); assert.equal(h.nodes['#cookie-banner'].hidden,true);
 assert.equal(h.cookies.get('martabucan_session'),'secure-session'); assert.equal(h.cookies.get('XSRF-TOKEN'),'csrf');
 h.mapButton.click(); assert(h.nodes['#map-container'].child.src.includes('/maps/embed')); assert.equal(h.scripts.length,0);
 assert.equal(JSON.parse(h.store.get('mb_consent_v2')).analytics,false);
 h.reject.click(); assert.equal(h.nodes['#map-container'].child,null);
}
{
 const h=harness(); h.settings.click(); h.nodes['#analytics-consent'].checked=true; h.nodes['#save-consent'].click();
 assert.equal(h.scripts.length,1); assert.equal(h.nodes['#map-container'].child,null);
 assert(h.scripts[0].src.includes('G-TEST123'));
 const calls=h.window.dataLayer.map(args=>Array.from(args));
 assert.equal(calls[0][2].analytics_storage,'denied'); assert.equal(calls[1][2].analytics_storage,'granted');
 assert.equal(calls[1][2].ad_user_data,'denied'); assert.equal(calls[1][2].ad_personalization,'denied');
 h.reject.click(); assert.equal(h.window['ga-disable-G-TEST123'],true); assert.equal(h.reloads(),1); assert.equal(h.cookies.has('_ga'),false); assert.equal(h.cookies.has('XSRF-TOKEN'),true);
}
{
 const h=harness({saved:choice({maps:true,analytics:true})}); assert.equal(h.scripts.length,1); assert(h.nodes['#map-container'].child);
 h.windowEvents.storage({key:'mb_consent_v2',newValue:JSON.stringify(choice())}); assert.equal(h.nodes['#map-container'].child,null); assert.equal(h.reloads(),1);
}
for(const saved of [choice({timestamp:Date.now()-181*86400000,maps:true,analytics:true}),choice({timestamp:Date.now()+60000}),choice({policy:'old-policy',maps:true,analytics:true})]) {
 const h=harness({saved}); assert.equal(h.scripts.length,0); assert.equal(h.nodes['#map-container'].child,null); assert.equal(h.nodes['#cookie-banner'].hidden,false);
}
{
 const h=harness({blockStorage:true}); h.mapButton.click(); assert(h.nodes['#map-container'].child); assert.equal(h.scripts.length,0); h.reject.click(); assert.equal(h.nodes['#map-container'].child,null);
}
{
 const h=harness({gaId:''}); h.accept.click(); assert.equal(h.scripts.length,0); assert.equal(JSON.parse(h.store.get('mb_consent_v2')).analytics,false);
}
console.log('Consent checks passed: no pre-consent requests, independent categories, withdrawal, expiry, cross-tab sync, blocked storage, Laravel cookies preserved.');
