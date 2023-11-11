var style = document.createElement('style');
style.setAttribute("id","multiselect_dropdown_styles");
style.innerHTML = `
.multiselect-dropdown{
  padding: 2px 5px 0px 5px;
  border-radius: 4px;
  border: solid 1px #ced4da;
  background-color: #a8a29e;
  position: relative;
  margin-bottom: 0.95rem;
}
.multiselect-dropdown span.optext, .multiselect-dropdown span.placeholder{
  margin-right:0.5em;
  margin-bottom:2px;
  padding:6px;
  border-radius: 4px;
  display:inline-block;
}
.multiselect-dropdown span.optext{
  background-color:#e3e3e3;
  padding:3px 0.75em;
  margin:5px 0px 5px 5px;
}
.multiselect-dropdown span.optext .optdel {
  float: right;
  margin: 0 -6px 1px 5px;
  font-size: 0.7em;
  margin-top: 2px;
  cursor: pointer;
  color: #666;
}
.multiselect-dropdown span.optext .optdel:hover { color: #c33;}
.multiselect-dropdown span.placeholder{
  color:#c4c4c4;
}
.multiselect-dropdown-list-wrapper{
  box-shadow: gray 0 3px 8px;
  z-index: 100;
  padding:2px;
  border-radius: 4px;
  border: solid 1px #ced4da;
  display: none;
  margin: -1px;
  position: absolute;
  top:0;
  left: 0;
  right: 0;
  background: #a8a29e;
}
.multiselect-dropdown-list-wrapper {
  margin-bottom:5px;
}

.multiselect-dropdown-search{
  margin-bottom:5px;
  background-color: #a8a29e;
}
.multiselect-dropdown-list{
  padding:2px;
  height: 15rem;
  overflow-y:auto;
  overflow-x: hidden;
}
.multiselect-dropdown-list::-webkit-scrollbar {
  width: 6px;
}
.multiselect-dropdown-list::-webkit-scrollbar-thumb {
  background-color: #bec4ca;
  border-radius:3px;
}

.multiselect-dropdown-list div{
  padding: 5px;
}
.multiselect-dropdown-list input{
  height: 1em;
  width: 1em;
  margin-right: 0.55em;
}
.multiselect-dropdown-list div.checked{
}
.multiselect-dropdown-list div:hover{
  background-color: #e3e3e3;
}
.multiselect-dropdown span.maxselected {width:95%;}
.multiselect-dropdown-all-selector {border-bottom:solid 1px #999;}
`;
document.head.appendChild(style);

function MultiselectDropdown(options){
    var config={
        search:true,
        height:'9rem',
        placeholder:'Seleziona',
        txtSelected:'Selezionati',
        txtAll:'Seleziona tutti',
        txtRemove: 'Rimuovi',
        txtSearch:'Cerca',
        ...options
    };
    function newEl(tag,attrs){
        var e=document.createElement(tag);
        if(attrs!==undefined) Object.keys(attrs).forEach(k=>{
            if(k==='class') { Array.isArray(attrs[k]) ? attrs[k].forEach(o=>o!==''?e.classList.add(o):0) : (attrs[k]!==''?e.classList.add(attrs[k]):0)}
            else if(k==='style'){
                Object.keys(attrs[k]).forEach(ks=>{
                    e.style[ks]=attrs[k][ks];
                });
            }
            else if(k==='text'){attrs[k]===''?e.innerHTML='&nbsp;':e.innerText=attrs[k]}
            else e[k]=attrs[k];
        });
        return e;
    }


    document.querySelectorAll("select[multiple]").forEach((el,k)=>{

        var div = newEl('div', {
            class: 'multiselect-dropdown',
            style: {
                width: '100%',
            }
        });        el.style.display='none';
        el.parentNode.insertBefore(div,el.nextSibling);
        var listWrap=newEl('div',{class:'multiselect-dropdown-list-wrapper'});
        var list=newEl('div',{class:'multiselect-dropdown-list',style:{height:config.height}});
        var search=newEl('input',{class:['multiselect-dropdown-search'].concat([config.searchInput?.class??'form-control']),style:{width:'100%',display:el.attributes['multiselect-search']?.value==='true'?'block':'none'},placeholder:config.txtSearch});
        listWrap.appendChild(search);
        div.appendChild(listWrap);
        listWrap.appendChild(list);

        el.loadOptions=()=>{
            list.innerHTML='';

            if(el.attributes['multiselect-select-all']?.value==='true'){
                var op=newEl('div',{class:'multiselect-dropdown-all-selector'})
                var ic=newEl('input',{type:'checkbox'});
                op.appendChild(ic);
                op.appendChild(newEl('label',{text:config.txtAll}));

                op.addEventListener('click',()=>{
                    op.classList.toggle('checked');
                    op.querySelector("input").checked=!op.querySelector("input").checked;

                    var ch=op.querySelector("input").checked;
                    list.querySelectorAll(":scope > div:not(.multiselect-dropdown-all-selector)")
                        .forEach(i=>{if(i.style.display!=='none'){i.querySelector("input").checked=ch; i.optEl.selected=ch}});

                    el.dispatchEvent(new Event('change'));
                });
                ic.addEventListener('click',(ev)=>{
                    ic.checked=!ic.checked;
                });
                el.addEventListener('change', (ev)=>{
                    let itms=Array.from(list.querySelectorAll(":scope > div:not(.multiselect-dropdown-all-selector)")).filter(e=>e.style.display!=='none')
                    let existsNotSelected=itms.find(i=>!i.querySelector("input").checked);
                    if(ic.checked && existsNotSelected) ic.checked=false;
                    else if(ic.checked==false && existsNotSelected===undefined) ic.checked=true;
                });

                list.appendChild(op);
            }

            Array.from(el.options).map(o=>{
                var op=newEl('div',{class:o.selected?'checked':'',optEl:o})
                var ic=newEl('input',{type:'checkbox',checked:o.selected});
                op.appendChild(ic);
                op.appendChild(newEl('label',{text:o.text}));

                op.addEventListener('click',()=>{
                    op.classList.toggle('checked');
                    op.querySelector("input").checked=!op.querySelector("input").checked;
                    op.optEl.selected=!!!op.optEl.selected;
                    el.dispatchEvent(new Event('change'));
                });
                ic.addEventListener('click',(ev)=>{
                    ic.checked=!ic.checked;
                });
                o.listitemEl=op;
                list.appendChild(op);
            });
            div.listEl=listWrap;

            div.refresh=()=>{
                div.querySelectorAll('span.optext, span.placeholder').forEach(t=>div.removeChild(t));
                var sels=Array.from(el.selectedOptions);
                if(sels.length>(el.attributes['multiselect-max-items']?.value??5)){
                    div.appendChild(newEl('span',{class:['optext','maxselected'],text:sels.length+' '+config.txtSelected}));
                }
                else{
                    sels.map(x=>{
                        var c=newEl('span',{class:'optext',text:x.text, srcOption: x});
                        if((el.attributes['multiselect-hide-x']?.value !== 'true'))
                            c.appendChild(newEl('span',{class:'optdel',text:'🗙',title:config.txtRemove, onclick:(ev)=>{c.srcOption.listitemEl.dispatchEvent(new Event('click'));div.refresh();ev.stopPropagation();}}));

                        div.appendChild(c);
                    });
                }
                if(0==el.selectedOptions.length) div.appendChild(newEl('span',{class:'placeholder',text:el.attributes['placeholder']?.value??config.placeholder}));
            };
            div.refresh();
        }
        el.loadOptions();

        search.addEventListener('input',()=>{
            list.querySelectorAll(":scope div:not(.multiselect-dropdown-all-selector)").forEach(d=>{
                var txt=d.querySelector("label").innerText.toUpperCase();
                d.style.display=txt.includes(search.value.toUpperCase())?'block':'none';
            });
        });

        div.addEventListener('click',()=>{
            div.listEl.style.display='block';
            search.focus();
            search.select();
        });

        document.addEventListener('click', function(event) {
            if (!div.contains(event.target)) {
                listWrap.style.display='none';
                div.refresh();
            }
        });
    });
}

window.addEventListener('load',()=>{
    MultiselectDropdown(window.MultiselectDropdownOptions);
});
