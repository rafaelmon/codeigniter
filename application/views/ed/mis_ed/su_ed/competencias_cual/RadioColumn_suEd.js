// JavaScript Document

// Modelo de columna para el checkbox
// crear columna tipo checkbox
Ext.grid.RadioColumn = function(config){
    Ext.apply(this, config);
    if(!this.id){
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
};
Ext.grid.RadioColumn.prototype =
{
    init: function(grid){
        this.grid = grid;
        this.grid.on('render', function(){
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
        }, this);
    },
    pintar_0: true,
    pintar_1: true,
    pintar_0_color: '#FF0000',
    pintar_1_color: '#298A08',
    width: 80,
    sortable: true,
    align:'center',
    menuDisabled:true,
    disabled: true,
    onMouseDown : function(e, t)
    {
        if(!this.readOnly){
            if(t.className && t.className.indexOf('x-grid3-cc-'+this.id) != -1){
                e.stopEvent();
                var index = this.grid.getView().findRowIndex(t);
                var record = this.grid.store.getAt(index);
                var resta;
                var suma;
                var div_cump=Ext.getCmp('div_total_cump');
                
                resta=((record.data['s_r1'])?1:0)*25
                      +((record.data['s_r2'])?1:0)*75  
                      +((record.data['s_r3'])?1:0)*100  
                      +((record.data['s_r4'])?1:0)*120;  
              
                for (var i=1; i<=this.q_radios; i++)
                    record.set(this.radioGroupClass+i, false);  
                record.set(this.dataIndex, !record.data[this.dataIndex]);  

                switch (this.dataIndex)
                {
                    case 's_r1': suma=25;break;
                    case 's_r2': suma=75;break;
                    case 's_r3': suma=100;break;
                    case 's_r4': suma=120;break;
                }
                T_CUMP=T_CUMP-resta+suma;
                
                CUMP = Math.round(T_CUMP/MAX_CUMP*100);
                
                div_cump.setText('<b>Peso M&aacute;ximo:'+MAX_CUMP+' - Peso ED:'+T_CUMP+' - Cumplimiento:'+CUMP+'%</b>');
                
                Ext.Ajax.request({
                    url: CARPETA+'/chek_radio',
                    success: function(response, options){
                        var planGrid=Ext.getCmp('edUsuarioPlanEditorGrid');
                        if(planGrid)
                        {
                            var planStore=planGrid.getStore();
                            planStore.reload();
                        }
                    },
                    failure: function(response, options){
                            Ext.MessageBox.alert("Error!", "Problemas al guardar cambios");
                    },
                    params: {id: record.data[this.campo_id], campo : this.dataIndex,value : record.data[this.dataIndex]  }
                });
                this.grid.store.commitChanges();
            };
        }
    },

    renderer : function(v, p, record){
        var suma=0;
        for (var i=1; i<=this.q_radios; i++)
            suma=suma+record.data[this.radioGroupClass+i];
        var pd = this.pintar_0;
        var pd_c;
        if (!suma)
            pd_c = this.pintar_0_color;
        else
            pd_c = '#FFF';
        var ph = this.pintar_1;
        var ph_c = this.pintar_1_color;
//        console.log(radio2);
        p.css += ' x-grid3-radio-col-td'; 
//        p.css += ' x-grid3-check-col-td'; 
        if (pd && !(v==1) ) 
        { 
//                p.css += ' celda_dehabilitado';
              p.attr = 'style="background-color:'+pd_c+'";';
        }
        if (ph && !(v==0) ) 
        { 
//                p.css += ' celda_dehabilitado';
              p.attr = 'style="background-color:'+ph_c+'";';
        }
        var vv;
        if (v==1) vv=true;
        else vv=false;
        return '<div class="x-grid3-radio-col'+(vv?'-on':'')+' x-grid3-cc-'+this.id+'"> </div>';
//        return '<div class="x-grid3-check-col'+(vv?'-on':'')+' x-grid3-cc-'+this.id+'"> </div>';
    }
};
		
/***************  Definir columna *****************/		
		
		