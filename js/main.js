(function(){
"use strict";

/* ---------- DATA ---------- */
var U=[
 {n:"Amity University Online",s:"AU",slug:"amity",t:"Online MBA available",p:["Multiple specializations","Fully online learning format"]},
 {n:"Manipal University Jaipur",s:"MUJ",slug:"manipal-jaipur",t:"Online MBA available",p:["Online classes and study material","Specialization choices"]},
 {n:"Sikkim Manipal University",s:"SMU",slug:"sikkim-manipal",t:"MBA programme offered",p:["Distance / online learning mode","Specialization options"]},
 {n:"VIT University",s:"VIT",slug:"vit",t:"MBA programme offered",p:["Established university option","Confirm current MBA mode with us"]},
 {n:"NMIMS Online",s:"NMIMS",slug:"nmims",t:"Online MBA available",p:["Online learning platform","Multiple specializations"]},
 {n:"GLA University Online",s:"GLA",slug:"gla",t:"Online MBA available",p:["Online learning platform","Multiple specializations"]},
 {n:"Dayananda Sagar University Online",s:"DSU",slug:"dayananda-sagar",t:"Online MBA available",p:["Online delivery format","Guidance on eligibility"]}
];
var SPEC=["Marketing","Finance","Human Resource Management","Business Analytics","Operations Management","International Business","IT / Information Systems","Not decided yet"];
var FAQ=[
 ["What is an Online MBA?","An Online MBA is a two-year postgraduate management degree delivered through a university's digital learning platform. Lectures, study material and assessments are accessed online, so you cover the same core subjects — finance, marketing, operations, strategy — without attending a campus."],
 ["Is an Online MBA suitable for working professionals?","Yes. Sessions are usually recorded and study material stays available, so you can study around shifts and travel. Most learners on these programmes are already working full time."],
 ["What is the eligibility for an Online MBA?","Typically a bachelor's degree from a recognised university, as per each university's own norms. Some also consider work experience or a qualifying test. Confirm exact requirements with your counsellor before applying."],
 ["How long does an Online MBA take?","Two years is the usual duration, split across four semesters. Some universities allow a longer completion window if you need extra time."],
 ["What MBA specializations are available?","Common streams include Marketing, Finance, Human Resource Management, Business Analytics, Operations Management, International Business and IT / Information Systems. The exact list varies by university and intake."],
 ["How much does an Online MBA cost?","Fees vary by university and are revised each intake, so we don't publish a fixed figure. Your counsellor shares the current fee and any EMI options directly from official university information."],
 ["Which university is best for an Online MBA?","There isn't one best option — it depends on your budget, specialization, career goal and how much support you want. A counsellor shortlists two or three that fit your profile instead of pointing everyone to the same name."],
 ["Can I pursue an Online MBA while working?","Yes, that's what the format is built for. You won't need to take leave or relocate, and exams are conducted online."],
 ["What is the admission process?","Apply online on the university portal, upload your documents (degree certificate, marksheets, ID proof, photograph), and pay the fee once your application is accepted. We help you at each step."],
 ["Are Online MBA degrees recognized?","Recognition depends on the university's approvals for that specific programme and intake. Always verify current recognition from official university sources before paying any fee — we'll show you where to check."]
];
/* PLACEHOLDER testimonials — invented copy, not real customers. Replace each
   entry with a genuine quote (with that person's consent) before relying on
   this section: publishing invented reviews is a consumer-protection issue,
   and India's CCPA framework on fake reviews covers exactly this. Format is
   [name, "Specialization \u00b7 City", quote]. */
var T=[
 ["Karthik Subramanian","IT / Information Systems \u00b7 Chennai","I had shortlisted five universities from Google and was more confused than when I started. One call narrowed it to two that actually fit my work hours."],
 ["Divya Ramesh","Business Analytics \u00b7 Bengaluru","They explained the difference between the online and distance formats properly, which no university website had made clear to me."],
 ["Anjali Nair","Finance \u00b7 Kochi","I'm a working mother, so weekend study was non-negotiable. They pointed me to the format that fit and helped with the paperwork."],
 ["Sai Kiran Reddy","Marketing \u00b7 Hyderabad","I asked about fees and got a straight answer with the EMI options, not a sales pitch. That is why I went ahead."],
 ["Lakshmi Priya","Operations Management \u00b7 Coimbatore","My degree was from 2014 and I assumed I had missed the window. The counsellor checked the eligibility norms and told me exactly which documents to arrange."],
 ["Naveen Varma","International Business \u00b7 Visakhapatnam","I travel for work most weeks. They filtered out the programmes with fixed live classes and left me with the ones I could actually finish."],
 ["Chaitra Hegde","Human Resource Management \u00b7 Mysuru","Follow-up came on WhatsApp, which suited me far better than calls during office hours."],
 ["Arun Menon","IT / Information Systems \u00b7 Thiruvananthapuram","I asked the same recognition question three times and got a patient answer each time, with a link to check it myself."],
 ["Vignesh Balaji","Finance \u00b7 Madurai","The comparison table saved me a week. Fees, duration and eligibility for every university on one screen."],
 ["Sravani Chowdary","Marketing \u00b7 Vijayawada","No pressure to decide on the call. They sent the details and let me take two weeks to think it over."],
 ["Manjunath Rao","Operations Management \u00b7 Mangaluru","My application was stuck at the document upload step and someone walked me through it on the phone the same evening."],
 ["Deepa Pillai","Business Analytics \u00b7 Kozhikode","I wanted a specialization that matched my current role rather than a generic MBA. They mapped the options against what I already do."]
];
var TICK=["Online MBA Admission 2026 — Applications Open","Free counselling for working professionals","Compare 7 universities in one call","Limited counselling slots today","EMI options available on most programmes"];
var NAMES=["Rohit from Delhi","Sneha from Mumbai","Arjun from Bengaluru","Kavita from Jaipur","Imran from Hyderabad","Neha from Lucknow","Vikram from Pune"];

/* ---------- UNIVERSITY LOGOS ---------- */
/* PHP resolves slug -> URL for logo files that actually exist (see
   mbag_university_logos in functions.php). A university with no file shows
   no logo slot at all — the card is just the name and its tag. Nothing is
   invented to fill the space. */
var LOGOS=(window.mbagSettings&&window.mbagSettings.uniLogos)||{},
    PLACEHOLDER=(window.mbagSettings&&window.mbagSettings.uniLogoPlaceholder)||"";

function esc(v){
  return String(v).replace(/&/g,"&amp;").replace(/"/g,"&quot;")
                  .replace(/</g,"&lt;").replace(/>/g,"&gt;");
}

/* @param u    University record.
   @param cls  Class for the wrapper element.
   @param alt  Alt text. Empty string = decorative (the name is beside it). */
function uniLogo(u,cls,alt){
  /* A university with no logo yet falls back to the shared placeholder, so
     the row of cards stays visually even while logos are still arriving. */
  var src=LOGOS[u.slug]||PLACEHOLDER;
  if(src){
    return '<span class="'+cls+' '+cls+'--img"><img src="'+esc(src)+'" alt="'+esc(alt)+'"'+
           ' loading="lazy" decoding="async" width="120" height="120"></span>';
  }
  return "";
}

/* ---------- TICKER ---------- */
var tk=document.getElementById("ticker");
if(tk){
  var line=TICK.map(function(t){return "<span>"+t+"</span>";}).join("");
  tk.innerHTML=line+line;
}

/* ---------- MARQUEE ---------- */
var mq=document.getElementById("marquee");
if(mq){
  var lg=U.map(function(u){return '<div class="mlogo">'+uniLogo(u,"mlogo__mark","")+esc(u.n)+'</div>';}).join("");
  mq.innerHTML=lg+lg;
}

/* ---------- UNIVERSITY CARDS + TABLE + SELECT ---------- */
/* The middle form's university select: the theme's own when that slot uses
   the static markup, otherwise the one inside the CF7 form. */
function talkUniSelect(){
  return document.getElementById("t-uni")
      || document.querySelector('.mbag-cf7--talk select[name="university"], .mbag-cf7--talk select[name="universities"]');
}

var g=document.getElementById("ugrid"),tb=document.getElementById("tbody"),us=document.getElementById("t-uni");
/* Gated on the grid and table only. It used to require the university select
   too, which silently emptied the whole section the moment a CF7 form
   replaced the static one. */
if(g&&tb){
  U.forEach(function(u){
    var c=document.createElement("article");
    c.className="uni rv";
    c.innerHTML='<div class="uni__top">'+uniLogo(u,"ulogo",u.n+" logo")+'<div><h3>'+esc(u.n)+'</h3>'+
      '<span class="tag'+(u.w?" warn":"")+'">'+u.t+'</span></div></div>'+
      '<ul>'+u.p.map(function(p){return "<li>"+p+"</li>";}).join("")+'</ul>'+
      '<div class="uni__cta">'+
        /* Low commitment: scrolls to the on-page form with this university
           already selected. */
        '<button class="btn btn--line" type="button" data-uni="'+esc(u.n)+'">Get Details</button>'+
        /* High intent: opens the popup, carrying the university with it so
           the lead says which one was clicked. */
        '<button class="btn btn--hot" type="button" data-mbag-popup'+
          ' data-mbag-popup-title="'+esc("Apply — "+u.n)+'"'+
          ' data-mbag-popup-sub="'+esc("Share your details and a counsellor will take you through eligibility, fees and the application.")+'"'+
          ' data-mbag-source="'+esc("Apply — "+u.n)+'"'+
          ' data-mbag-university="'+esc(u.n)+'">Apply Now</button>'+
      '</div>';
    g.appendChild(c);

    var tr=document.createElement("tr");
    tr.innerHTML="<td>"+u.n+"</td><td>Online / Distance MBA</td><td>2 years (typical)</td>"+
      "<td>Bachelor's degree (as per university norms)</td><td>Multiple — confirm current list</td>"+
      '<td class="fee">'+
        '<button type="button" class="fee__btn" data-mbag-popup'+
        ' data-mbag-popup-title="'+esc("Fees — "+u.n)+'"'+
        ' data-mbag-popup-sub="'+esc("Share your details and a counsellor will send the current fee structure and EMI options.")+'"'+
        ' data-mbag-source="'+esc("Fee enquiry — "+u.n)+'"'+
        ' data-mbag-university="'+esc(u.n)+'">Check Current Fee</button>'+
      '</td><td>Online classes + online exams</td>'+
      "<td>Apply online, submit documents, fee payment</td>";
    tb.appendChild(tr);

    if(us){var o=document.createElement("option");o.textContent=u.n;us.appendChild(o);}
  });
}

/* ---------- SPECIALIZATION SELECTS + CHIPS ---------- */
/* Any select that should hold the specialization list - the theme's own
   .js-spec selects, plus a Contact Form 7 select named "spec". */
function specSelects(){
  return document.querySelectorAll('.js-spec, .mbag-form select[name="spec"], .mbag-form select[name="specialization"]');
}
specSelects().forEach(function(sel){
  /* A CF7 select already ships its own <option> list from the form tag, so
     only fill in selects that are still empty (placeholder option only). */
  if(sel.options.length>1)return;
  SPEC.forEach(function(s){var o=document.createElement("option");o.textContent=s;sel.appendChild(o);});
});
var ch=document.getElementById("chips");
if(ch){
  SPEC.slice(0,7).forEach(function(s){
    var b=document.createElement("button");b.type="button";b.className="chip";b.textContent=s;
    b.addEventListener("click",function(){
      specSelects().forEach(function(sel){sel.value=s;});
      go(document.getElementById("talk"));
      flash(document.getElementById("t-spec")||document.querySelector('.mbag-cf7--talk select'));
    });
    ch.appendChild(b);
  });
}

/* ---------- TESTIMONIALS ---------- */
var tg=document.getElementById("tgrid");
if(tg){
  T.forEach(function(t){
    var d=document.createElement("div");d.className="tcard rv";
    var stars=(window.mbagSettings&&window.mbagSettings.starIcon)||"";
    d.innerHTML='<div class="stars" role="img" aria-label="Rated 5 out of 5">'+stars.repeat(5)+'</div><p>"'+esc(t[2])+'"</p>'+
      '<div class="twho"><div class="av" aria-hidden="true">'+esc(t[0].charAt(0))+'</div><div><b>'+esc(t[0])+'</b><span>'+esc(t[1])+'</span></div></div>';
    tg.appendChild(d);
  });
}

/* ---------- FAQ ---------- */
var fq=document.getElementById("faq");
if(fq){
  FAQ.forEach(function(f,i){
    var d=document.createElement("div");d.className="fitem";
    d.innerHTML='<button class="fq" type="button" aria-expanded="false" aria-controls="fa'+i+'">'+f[0]+'</button>'+
      '<div class="fa" id="fa'+i+'" role="region"><p>'+f[1]+'</p></div>';
    fq.appendChild(d);
  });
  fq.addEventListener("click",function(e){
    var q=e.target.closest(".fq");if(!q)return;
    var it=q.parentElement,pa=it.querySelector(".fa"),open=it.classList.contains("open");
    fq.querySelectorAll(".fitem.open").forEach(function(el){
      el.classList.remove("open");el.querySelector(".fa").style.maxHeight=null;
      el.querySelector(".fq").setAttribute("aria-expanded","false");
    });
    if(!open){it.classList.add("open");pa.style.maxHeight=pa.scrollHeight+"px";q.setAttribute("aria-expanded","true");}
  });
}

/* ---------- GET DETAILS ---------- */
document.addEventListener("click",function(e){
  var b=e.target.closest("[data-uni]");if(!b)return;
  var sel=talkUniSelect(),name=b.getAttribute("data-uni");
  if(sel){
    /* Only take the value if that option exists, so a name the form does not
       offer leaves the select alone rather than setting something invalid. */
    var match=Array.prototype.filter.call(sel.options,function(o){
      return o.value===name||o.textContent.trim()===name;
    })[0];
    if(match)sel.value=match.value;
  }
  go(document.getElementById("talk"));flash(sel);
});

/* ---------- FORMS ---------- */
/* NOTE: This validates fields and shows a success message client-side only.
   To actually receive leads by email/CRM, connect these forms to a form
   plugin (e.g. WPForms, Contact Form 7) or wire up a custom
   admin-ajax.php / REST handler in functions.php. */
var re=/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
document.querySelectorAll(".js-form").forEach(function(f){
  var ph=f.querySelector('[data-t="phone"]');
  if(ph)ph.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").slice(0,10);});

  f.addEventListener("submit",function(e){
    e.preventDefault();var ok=true;
    f.querySelectorAll("[data-req]").forEach(function(i){
      var fl=i.closest(".field"),v=(i.value||"").trim(),good=v!=="";
      if(good&&i.dataset.t==="phone")good=/^[6-9]\d{9}$/.test(v);
      if(good&&i.dataset.t==="email")good=re.test(v);
      fl.classList.toggle("err",!good);
      if(!good&&ok){i.focus();ok=false;}
    });
    if(!ok)return;
    var b=f.querySelector('button[type="submit"]'),lb=b.innerHTML;
    b.innerHTML="Sending…";b.disabled=true;
    setTimeout(function(){
      f.querySelector(".ok").classList.add("show");
      b.innerHTML=lb;b.disabled=false;f.reset();
    },700);
  });
  ["input","change"].forEach(function(ev){
    f.addEventListener(ev,function(e){var fl=e.target.closest(".field");if(fl)fl.classList.remove("err");});
  });
});

/* ---------- COUNTDOWN ---------- */
var end=Date.now()+2*60*60*1000,ck=document.getElementById("clock");
if(ck){
  setInterval(function(){
    var d=Math.max(0,end-Date.now()),h=Math.floor(d/3.6e6),m=Math.floor(d%3.6e6/6e4),s=Math.floor(d%6e4/1000);
    ck.textContent=[h,m,s].map(function(x){return String(x).padStart(2,"0");}).join(":");
  },1000);
}

/* ---------- COUNTERS ---------- */
function count(el){
  var to=+el.dataset.count,sfx=el.dataset.suffix||"",st=performance.now(),dur=1400;
  (function step(now){
    var p=Math.min(1,(now-st)/dur),e=1-Math.pow(1-p,3);
    el.textContent=Math.round(to*e).toLocaleString("en-IN")+sfx;
    if(p<1)requestAnimationFrame(step);
  })(st);
}

/* ---------- REVEAL + COUNTER OBSERVER ---------- */
if("IntersectionObserver" in window){
  var io=new IntersectionObserver(function(es){
    es.forEach(function(en){if(en.isIntersecting){en.target.classList.add("in");io.unobserve(en.target);}});
  },{threshold:.12,rootMargin:"0px 0px -40px 0px"});
  document.querySelectorAll(".rv").forEach(function(el,i){el.style.transitionDelay=(i%3)*80+"ms";io.observe(el);});

  var co=new IntersectionObserver(function(es){
    es.forEach(function(en){if(en.isIntersecting){count(en.target);co.unobserve(en.target);}});
  },{threshold:.5});
  document.querySelectorAll("[data-count]").forEach(function(el){co.observe(el);});
}else{
  document.querySelectorAll(".rv").forEach(function(el){el.classList.add("in");});
  document.querySelectorAll("[data-count]").forEach(count);
}

/* ---------- PROGRESS BAR ---------- */
var pb=document.getElementById("progress");
if(pb){
  window.addEventListener("scroll",function(){
    var h=document.documentElement.scrollHeight-window.innerHeight;
    pb.style.width=(h>0?window.scrollY/h*100:0)+"%";
  },{passive:true});
}

/* ---------- SOCIAL PROOF TOAST ---------- */
var toast=document.getElementById("toast"),tt=document.getElementById("toastText"),ti=0;
if(toast&&tt){
  function ping(){
    var n=NAMES[ti++%NAMES.length],m=2+Math.floor(Math.random()*20);
    tt.innerHTML="<b>"+n+"</b> requested MBA counselling · "+m+" min ago";
    toast.classList.add("show");
    setTimeout(function(){toast.classList.remove("show");},5000);
  }
  setTimeout(function(){ping();setInterval(ping,16000);},6000);
}

/* ---------- HELPERS ---------- */
function go(el){if(!el)return;window.scrollTo({top:el.getBoundingClientRect().top+window.pageYOffset-80,behavior:"smooth"});}
function flash(el){if(!el)return;setTimeout(function(){el.style.boxShadow="0 0 0 4px rgba(255,176,32,.5)";setTimeout(function(){el.style.boxShadow="";},1300);},500);}
var yr=document.getElementById("yr");
if(yr)yr.textContent=new Date().getFullYear();
})();


/* ---------- CONTACT FORM 7 ---------- */
/* CF7 handles its own validation, AJAX submit and success message, so the
   .js-form handler above deliberately does not touch .mbag-form. These are
   the two behaviours worth keeping from the static forms. */

/* 1. Digits-only, max 10, on the mobile field. */
document.querySelectorAll('.mbag-form input[type="tel"]').forEach(function(ph){
  ph.setAttribute("inputmode","numeric");
  ph.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").slice(0,10);});
});

/* 2. Scroll the success banner into view after a successful send. */
document.addEventListener("wpcf7mailsent",function(e){
  var out=e.target.querySelector(".wpcf7-response-output");
  if(out&&out.scrollIntoView)out.scrollIntoView({behavior:"smooth",block:"center"});
},false);


/* ---------- MOBILE NAVIGATION ---------- */
(function(){
  var toggle=document.getElementById("navToggle"),nav=document.getElementById("primaryNav");
  if(!toggle||!nav)return;

  function setOpen(open){
    document.body.classList.toggle("nav-open",open);
    toggle.setAttribute("aria-expanded",open?"true":"false");
  }

  toggle.addEventListener("click",function(){
    setOpen(!document.body.classList.contains("nav-open"));
  });

  /* Close after tapping a link, and on Escape. */
  nav.addEventListener("click",function(e){
    if(e.target.closest("a"))setOpen(false);
  });
  document.addEventListener("keydown",function(e){
    if(e.key==="Escape"&&document.body.classList.contains("nav-open")){setOpen(false);toggle.focus();}
  });
  window.addEventListener("resize",function(){
    if(window.innerWidth>900)setOpen(false);
  });
})();


/* ---------- THANK YOU REDIRECT ---------- */
/* CF7 removed on_sent_ok in v5.0, so the supported way to send a lead to the
   Thank You page is this DOM event. Set the page under
   Appearance > Customize > Lead Forms; with nothing set, nothing happens and
   CF7's own inline success message stays. */
document.addEventListener("wpcf7mailsent",function(){
  var url=(window.mbagSettings&&window.mbagSettings.thankYou)||"";
  if(!url)return;
  window.setTimeout(function(){window.location.href=url;},700);
},false);


/* ---------- LEAD POPUP ---------- */
/* One modal, three triggers: a timer, a scroll-depth mark, and any element
   marked data-mbag-popup (or given the class .mbag-popup). The automatic
   ones fire once per browser session
   and never on the Thank You page — someone who already converted should
   not be asked again. Button clicks always open it. */
(function(){
  var modal=document.getElementById("mbagPopup");
  if(!modal)return;

  /* Every element that opens the popup, in one place.
     - the explicit attribute / class, for anything you mark yourself
     - #apply and #talk, the two lead CTAs, on every page including the
       landing page
     Navigation anchors (#compare, #universities, #specializations, #faqs)
     are deliberately absent: they take the visitor somewhere on the page,
     they are not asks. */
  var TRIGGERS='[data-mbag-popup],.mbag-popup,a[href$="#apply"],a[href$="#talk"]';

  var S=window.mbagSettings||{},
      opts=S.popup||{},
      dialog=modal.querySelector(".mbag-modal__dialog"),
      titleEl=modal.querySelector("#mbagPopupTitle"),
      subEl=modal.querySelector(".card__head p"),
      KEY="mbagPopupSeen",
      lastFocus=null;

  /* The wording the popup returns to when opened by anything that does not
     override it (the timer, the scroll mark, a plain CTA). */
  var defTitle=titleEl?titleEl.textContent:"",
      defSub=subEl?subEl.textContent:"";

  /* Match the popup's wording to whatever was clicked: someone who asked for
     a brochure should not be met with a counselling headline. A trigger can
     also name itself, which is written into a form field called "source" if
     the CF7 form has one — that is how the lead tells you where it came from. */
  function applyTriggerCopy(trigger){
    var t=trigger&&trigger.getAttribute("data-mbag-popup-title"),
        b=trigger&&trigger.getAttribute("data-mbag-popup-sub"),
        src=trigger&&trigger.getAttribute("data-mbag-source");

    if(titleEl)titleEl.textContent=t||defTitle;
    if(subEl){
      subEl.textContent=b||defSub;
      subEl.hidden=!subEl.textContent;
    }

    var field=modal.querySelector('input[name="source"],input[name="mbag-source"]');
    if(field)field.value=src||"";

    /* A trigger tied to one university pre-selects it, so the counsellor
       knows which fee was asked about without the visitor retyping it.
       Silently skipped when the form has no such field. */
    var uni=trigger&&trigger.getAttribute("data-mbag-university"),
        uniField=modal.querySelector('select[name="universities"],select[name="university"],input[name="university"]');
    if(uniField){
      if(uni&&uniField.tagName==="SELECT"){
        /* Only take the value if the form actually offers that option. */
        var match=Array.prototype.filter.call(uniField.options,function(o){
          return o.value===uni||o.textContent.trim()===uni;
        })[0];
        uniField.value=match?match.value:"";
      }else{
        uniField.value=uni||"";
      }
    }
  }

  var FOCUSABLE='a[href],button:not([disabled]),input:not([disabled]):not([type="hidden"]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';

  function seen(){try{return sessionStorage.getItem(KEY)==="1";}catch(e){return false;}}
  function markSeen(){try{sessionStorage.setItem(KEY,"1");}catch(e){}}
  function isOpen(){return modal.classList.contains("is-open");}

  function open(auto,trigger){
    if(isOpen())return;
    applyTriggerCopy(trigger);
    if(auto){
      if(seen()||document.body.classList.contains("is-thankyou"))return;
      markSeen();
    }
    lastFocus=document.activeElement;
    modal.hidden=false;
    document.body.classList.add("mbag-noscroll");
    /* Next frame, so the transition runs from the hidden state. */
    window.requestAnimationFrame(function(){modal.classList.add("is-open");});

    var first=dialog.querySelector('input:not([type="hidden"]),select,textarea');
    (first||dialog.querySelector(".mbag-modal__close")).focus({preventScroll:true});
  }

  function close(){
    if(!isOpen())return;
    modal.classList.remove("is-open");
    document.body.classList.remove("mbag-noscroll");
    /* Keep it out of the tab order once the transition has finished. */
    window.setTimeout(function(){if(!isOpen())modal.hidden=true;},260);
    if(lastFocus&&lastFocus.focus)lastFocus.focus({preventScroll:true});
  }

  /* Close: overlay, close button, Escape. */
  modal.addEventListener("click",function(e){
    if(e.target.closest("[data-mbag-close]"))close();
  });
  document.addEventListener("keydown",function(e){
    if(!isOpen())return;
    if(e.key==="Escape"){close();return;}
    if(e.key!=="Tab")return;
    /* Focus trap — a modal that leaks focus to the page behind it is not a modal. */
    var items=dialog.querySelectorAll(FOCUSABLE);
    if(!items.length)return;
    var first=items[0],last=items[items.length-1];
    if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus();}
    else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus();}
  });

  /* Manual triggers — see TRIGGERS above. The .mbag-popup class is there
     because menu items accept a CSS class but not a data attribute, so it
     is the only way to trigger the popup from Appearance > Menus.

     Not included: the per-university "Get Details" buttons and the
     specialization chips. Those pre-fill the university or specialization
     into the inline form, and a generic popup would throw that away. */
  document.addEventListener("click",function(e){
    var t=e.target.closest(TRIGGERS);
    if(!t)return;
    e.preventDefault();
    open(false,t);
  });

  /* Timer trigger. */
  var delay=parseInt(opts.delay,10)||0;
  if(delay>0)window.setTimeout(function(){open(true);},delay*1000);

  /* Scroll-depth trigger. */
  var pct=parseInt(opts.scroll,10)||0;
  if(pct>0){
    var onScroll=function(){
      var h=document.documentElement.scrollHeight-window.innerHeight;
      if(h<=0)return;
      if((window.pageYOffset/h)*100>=pct){
        window.removeEventListener("scroll",onScroll);
        open(true);
      }
    };
    window.addEventListener("scroll",onScroll,{passive:true});
  }

  /* A submitted popup form should not sit open behind the redirect. */
  document.addEventListener("wpcf7mailsent",function(e){
    if(modal.contains(e.target))window.setTimeout(close,600);
  },false);
})();
