Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return false;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        return true;
    }
});
//Filtros
arrayfiltroCriticidad = new Ext.data.JsonStore({
    url: CARPETA+'/filtroCriticidad',
    root: 'rows',
//        method: 'POST',
    fields: ['id_criticidad', 'criticidad']
//        autoload: true
});
//arrayfiltroCriticidad.load();	
arrayfiltroCriticidad.on('load' , function(  js , records, options ){
    var tRecord = Ext.data.Record.create(
        {name: 'id_criticidad', type: 'int'},
        {name: 'criticidad',    type: 'string'}
    );
    var myNewT = new tRecord({
        id_criticidad: '-1',
        criticidad: 'Todas'
    });
	arrayfiltroCriticidad.insert( 0, myNewT);	
});
arrayfiltroEstado = new Ext.data.JsonStore({
    url: CARPETA+'/filtroEstado',
    root: 'rows',
//        method: 'POST',
    fields: ['id_estado', 'estado']
//        autoload: true
});
//arrayfiltroCriticidad.load();	
arrayfiltroEstado.on('load' , function(  js , records, options ){
    var tRecord = Ext.data.Record.create(
        {name: 'id_estado', type: 'int'},
        {name: 'estado',    type: 'string'}
    );
    var myNewT = new tRecord({
        id_estado: '-1',
        estado: 'Todos'
    });
	arrayfiltroCriticidad.insert( 0, myNewT);	
});
cppRepoFallasSectoresJS = new Ext.data.JsonStore({
    url: CARPETA+'/filtroSectores',
    root: 'rows',
//        method: 'POST',
    fields: ['id_sector', 'sector']
//        autoload: true
});
//arrayfiltroCriticidad.load();	
//cppRepoFallasSectoresJS.on('load' , function(  js , records, options ){
//    var tRecord = Ext.data.Record.create(
//        {name: 'id_sector', type: 'int'},
//        {name: 'sector',    type: 'string'}
//    );
//    var myNewT = new tRecord({
//        id_sector: '-1',
//        sector: 'Todos'
//    });
//	cppRepoFallasSectoresJS.insert( 0, myNewT);	
//});
cppRepoFallasEquiposJS = new Ext.data.JsonStore({
    url: CARPETA+'/filtroEquipos',
    root: 'rows',
//        method: 'POST',
    fields: ['id_equipo', 'equipo']
//        autoload: true
});
//arrayfiltroCriticidad.load();	
//cppRepoFallasEquiposJS.on('load' , function(  js , records, options ){
//    var tRecord = Ext.data.Record.create(
//        {name: 'id_equipo', type: 'int'},
//        {name: 'equipo',    type: 'string'}
//    );
//    var myNewT = new tRecord({
//        id_equipo: '-1',
//        equipo: 'Todos'
//    });
//	cppRepoFallasEquiposJS.insert( 0, myNewT);	
//});






var cppRepoFallasFiltroCriticidad = new Ext.form.ComboBox({
    id:'cppRepoFallasFiltroCriticidad',
    fieldLabel: 'Criticidad',
    forceSelection : true,
    value: 'Todas',
    store: arrayfiltroCriticidad,
    edicppRepoFallasle : false,
    displayField: 'criticidad',
    valueField:'id_criticidad',
    allowBlank: true,
    selectOnFocus:true,
    anchor : '98%',
//    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});

var cppRepoFallasFiltroEstado = new Ext.form.ComboBox({
    id:'cppRepoFallasFiltroEstado',
    fieldLabel: 'Estado',
    forceSelection : true,
    autoScroll:true,
    value: 'Todos',
    store: arrayfiltroEstado,
    edicppRepoFallasle : false,
    displayField: 'estado',
    valueField:'id_estado',
    allowBlank: true,
    selectOnFocus:true,
    anchor : '98%',
//    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});
var cppRepoFallasFiltroFechaDesde = new Ext.form.DateField({
    id:'cppRepoFallasFiltroFechaDesde',
    fieldLabel: 'Desde',
    vtype: 'daterange',
    anchor : '98%',
    endDateField: 'cppRepoFallasFiltroFechaHasta'
});
var cppRepoFallasFiltroFechaHasta = new Ext.form.DateField({
    id:'cppRepoFallasFiltroFechaHasta',
    fieldLabel: 'Hasta',
    vtype: 'daterange',
    anchor : '98%',
    startDateField: 'cppRepoFallasFiltroFechaDesde'
});

cppRepoFallasFiltroFechas=new Ext.form.CompositeField({
    fieldLabel: 'Fecha Evento',
    combineErrors: false,
    items: [
//        {xtype: 'displayfield', value: ' Desde>'},
        cppRepoFallasFiltroFechaDesde,
        {xtype: 'displayfield', value: '<..>'},
        cppRepoFallasFiltroFechaHasta,
    ]
});

var cppRepoFallasFiltroSectores = new Ext.ux.form.SuperBoxSelect({
    id:'cppRepoFallasFiltroSectores',
    fieldLabel: 'Sectores',
    forceSelection : true,
    autoScroll:true,
    value: 'Todos',
    store: cppRepoFallasSectoresJS,
    edicppRepoFallasle : false,
    displayField: 'sector',
    valueField:'id_sector',
    allowBlank: true,
    selectOnFocus:true,
    anchor : '95%',
//    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});
var cppRepoFallasFiltroEquipos = new Ext.ux.form.SuperBoxSelect({
    id:'cppRepoFallasFiltroEquipos',
    fieldLabel: 'Equipos',
    forceSelection : true,
    autoScroll:true,
    value: 'f_all',
    store: cppRepoFallasEquiposJS,
    edicppRepoFallasle : false,
    displayField: 'equipo',
    valueField:'id_equipo',
    allowBlank: true,
    selectOnFocus:true,
    anchor : '95%',
//    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});

var cppRepoFallasEspaciador = new Ext.Spacer({
    id:'cppRepoFallasEspaciador',
    height :15
//    clearFilterOnReset : false
});
//Fin Filtros
cppRepoFallasFiltrosPanel = new Ext.FormPanel(
{
        id: 'cppRepoFallasFiltrosPanel',
        frame: false,
        collapsible: false,
        labelAlign: 'right',
        labelWidth: 80,
        split: false,
        header: false,
        region:'north',
        height: 150,
        margins: '5 5 5 5',
        bodyStyle: Ext.isIE ? 'padding:0 0 5px 15px;' : 'padding:10px 15px;',
        layout: 'column',
        items:[{
                    columnWidth:.35,
                    layout:'form',
                    border:false,
                    items:[cppRepoFallasFiltroCriticidad]
                },
                {
                    columnWidth:.35,
                    layout: 'form',
                    border:false,
                    items: [cppRepoFallasFiltroEstado]
                },
                {
                    columnWidth:.15,
                    layout: 'form',
                    border:false,
                    items: [cppRepoFallasFiltroFechaDesde]
                },
                {
                    columnWidth:.15,
                    layout: 'form',
                    border:false,
                    items: [cppRepoFallasFiltroFechaHasta]
                },
                {
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [cppRepoFallasEspaciador]
                },
                {
                    columnWidth:.5,
                    layout: 'form',
                    border:false,
                    items: [cppRepoFallasFiltroSectores]
                },
                {
                    columnWidth:.5,
                    layout: 'form',
                    border:false,
                    items: [cppRepoFallasFiltroEquipos]
                }
                ],
        buttons: [{
//                text: 'Filtrar',
//                handler: ''
//            },{
                text: 'Limpiar filtros',
                handler: cppRepoFallasClickBtnLFiltros
            },{
                text: 'Descargar listado',
    //            tooltip: 'e...',
                iconCls:'archivo_excel_ico',
                handler: cppControlFallosClickBtnExcel
            }
        ]
});

altura=Ext.getBody().getSize().height - 60;
//cppRepoFallasFiltrosPanel.setHeight(altura);
//Ext.getCmp('browser').on('resize',function(comp){
//    cppRepoFallasFiltrosPanel.setWidth(this.getSize().width);
//    cppRepoFallasFiltrosPanel.setHeight(Ext.getBody().getSize().height - 60);
//});

cppRepoFallasFiltroCriticidad.on('select', filtrarGrillaRepoFallas);
cppRepoFallasFiltroEstado.on('select', filtrarGrillaRepoFallas);
cppRepoFallasFiltroFechaDesde.on('select', filtrarGrillaRepoFallas);
cppRepoFallasFiltroFechaHasta.on('select', filtrarGrillaRepoFallas);
cppRepoFallasFiltroSectores.on('additem', filtrarGrillaRepoFallas);
cppRepoFallasFiltroSectores.on('removeitem', filtrarGrillaRepoFallas);
cppRepoFallasFiltroEquipos.on('additem', filtrarGrillaRepoFallas);
cppRepoFallasFiltroEquipos.on('removeitem', filtrarGrillaRepoFallas);
function filtrarGrillaRepoFallas( combo, record, index ){
        var id_criticidad = Ext.getCmp('cppRepoFallasFiltroCriticidad').getValue();
        var id_estado = Ext.getCmp('cppRepoFallasFiltroEstado').getValue();
        var f_ini = Ext.getCmp('cppRepoFallasFiltroFechaDesde').getValue();
        var f_fin = Ext.getCmp('cppRepoFallasFiltroFechaHasta').getValue();
        var sectores = Ext.getCmp('cppRepoFallasFiltroSectores').getValue();
        var equipos = Ext.getCmp('cppRepoFallasFiltroEquipos').getValue();
        
        var values = [];
            values.push(id_criticidad);
            values.push(id_estado);
            values.push(f_ini);
            values.push(f_fin);
            values.push(sectores);
            values.push(equipos);
            
	var encoded_array_v = Ext.encode(values);
        
        cppRepoFallasDataStore.setBaseParam('filtros',encoded_array_v);
        cppRepoFallasDataStore.load();
    
};
function cppRepoFallasClickBtnLFiltros(){
    var grillaEventos=Ext.getCmp('cppRepoFallasGridPanel');
    var grillaDS= grillaEventos.getStore();
    var filtro1=Ext.getCmp('cppRepoFallasFiltroCriticidad');
    var filtro2=Ext.getCmp('cppRepoFallasFiltroEstado');
    var filtro3=Ext.getCmp('cppRepoFallasFiltroFechaDesde');
    var filtro4=Ext.getCmp('cppRepoFallasFiltroFechaHasta');
    var filtro5=Ext.getCmp('cppRepoFallasFiltroSectores');
    var filtro6=Ext.getCmp('cppRepoFallasFiltroEquipos');
    filtro1.reset();
    filtro2.reset();
    filtro3.reset();
    filtro4.reset();
    filtro5.reset();
    filtro6.reset();
    grillaDS.setBaseParam('filtros','');
    grillaDS.load();
};