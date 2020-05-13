misEeddDataStore = new Ext.data.GroupingStore({
      id: 'misEeddDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_evaluacion'
      },[ 
        {name: 'id_evaluacion',         type: 'int',    mapping: 'id_evaluacion'},        
        {name: 'id_periodo',            type: 'int',    mapping: 'id_periodo'},
        {name: 'periodo',               type: 'string', mapping: 'periodo'},
        {name: 'id_estado',             type: 'int',    mapping: 'id_estado'},
        {name: 'tipo',                  type: 'int',    mapping: 'tipo'},
        {name: 'estado',                type: 'string', mapping: 'estado'},
        {name: 'id_avance',             type: 'int',    mapping: 'id_avance'},
        {name: 'avance',                type: 'string', mapping: 'avance'},
        {name: 'a_c1',                  type: 'int', mapping: 'a_c1'},
        {name: 'a_c2',                  type: 'int', mapping: 'a_c2'},
        {name: 'a_fyam',                type: 'int', mapping: 'a_fyam'},
        {name: 'a_pm',                  type: 'int', mapping: 'a_pm'},
        {name: 'a_fm',                  type: 'int', mapping: 'a_fm'},
        {name: 'id_usuario_supervisor', type: 'int',    mapping: 'id_usuario_supervisor'},
        {name: 'usuario',               type: 'string', mapping: 'usuario'},
        {name: 'fecha_cierre_u',        type: 'string', mapping: 'fecha_cierre_u'},
        {name: 'supervisor',            type: 'string', mapping: 'supervisor'},
        {name: 'fecha_cierre_s',        type: 'string', mapping: 'fecha_cierre_s'},
        {name: 'v_peso',                type: 'string', mapping: 'v_peso'},
        {name: 'v_cump',                type: 'string', mapping: 'v_cump'}
//        {name: 'habilitado', type: 'bool', mapping: 'habilitado'}
      ]),
      sortInfo:{field: 'id_periodo', direction: "ASC"},
      groupField:'tipo',
      remoteSort: true
    });
    
var paginador= new Ext.PagingToolbar({
    pageSize: parseInt(TAM_PAGINA),
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Primera Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});
    paginador.bindStore(misEeddDataStore);

miEdBotonesAction = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Acción',
    hideable:false,
    align:'center',
    width:  80,
//    tooltip:'',
    hidden:false,
    items:[
        {
        icon:URL_BASE+'images/list-edit.png',
        iconCls :'col_accion',
        tooltip:'Editar mi autoevaluación',
        getClass: showBtnEditarMiED,
        handler: clickBtnEditarMiED
    },{
        icon:URL_BASE+'images/list-supervisor.png',
        iconCls :'col_accion',
        tooltip:'Supervisar evaluacion',
        getClass: showBtnSupervisarED,
        handler: clickBtnSupervisarED
    },{
        icon:URL_BASE+'images/doc_pdf.png',
        iconCls :'col_accion',
//      getClass: showBtnModificar,
        tooltip:'Ver evaluación',
        handler: clickBtnVerEDPdf
    },{
        icon:URL_BASE+'images/goal.png',
        iconCls :'col_accion',
        getClass: showBtnCerrar.createDelegate(this,'btn_si',true),
        tooltip:'Cerrar evaluación',
        handler: clickBtnCerrarED
    },{
        icon:URL_BASE+'images/no_goal.png',
        iconCls :'col_accion',
        getClass: showBtnCerrar.createDelegate(this,'btn_no',true),
        tooltip:'Cerrar evaluación',
        handler: clickBtnCerrarED_no
    }
]
});

misEeddColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_evaluacion',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Tipo',
        dataIndex: 'tipo',
        hidden: true,
        width: 100,
        sortable: false,
        renderer: showTipo
      },{
        header: 'Periodo',
        dataIndex: 'periodo',
        width: 100,
        sortable: true
      },{
        header: 'Usuario',
        dataIndex: 'usuario',
        width:  150,
        sortable: true,
      },{
        header: 'Supervisor',
        dataIndex: 'supervisor',
        width:  150,
        sortable: true
      },{
        header: 'Estado',
        dataIndex: 'estado',
        width:  90,
        sortable: true,
        renderer: showEstado
//      },{
//        header: 'Avance',
//        dataIndex: 'avance',
//        width:  90,
//        sortable: true,
//        renderer: showEstado
      },{
        header: 'C1',
        tooltip:'Avance en evaluación de competencias cualitativas',
        align:'center',
        dataIndex: 'a_c1',
        width:  40,
        sortable: false,
        renderer: showAvance.createDelegate(this,'c1',true)
      },{
        header: 'C2',
        tooltip:'Avance en evaluación de competencias cuantitativas',
        align:'center',
        dataIndex: 'a_c2',
        width:  40,
        sortable: false,
        renderer: showAvance
      },{
        header: 'FyAM',
        tooltip:'Avance en definición de fortalezas y aspectos a mejorar',
        align:'center',
        dataIndex: 'a_fyam',
        width:  40,
        sortable: false,
        renderer: showAvance
      },{
        header: 'PM',
        tooltip:'Definición de planes de mejora',
        align:'center',
        dataIndex: 'a_pm',
        width:  40,
        sortable: false,
        renderer: showAvance
      },{
        header: 'FM',
        tooltip:'Fijación de metas',
        align:'center',
        dataIndex: 'a_fm',
        width:  40,
        sortable: false,
        renderer: showAvanceOptativo
      },{
        header: 'Peso',
        tooltip:'Peso ED',
        align:'center',
        dataIndex: 'v_peso',
        width:  55,
        sortable: false,
        renderer: showCumplimiento
      },{
        header: '% Cump',
        tooltip:'Peso ED',
        align:'center',
        dataIndex: 'v_cump',
        width:  55,
        sortable: false,
        renderer: showCumplimiento
      },{
        header: 'Cierre superevisor',
        dataIndex: 'fecha_cierre_s',
        sortable: true,
        width:  70,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Cierre usuario',
        dataIndex: 'fecha_cierre_u',
        sortable: true,
        width:  70,
        fixed:true,
        readOnly: true,
        align:'center'
      },miEdBotonesAction]
    );
    
    buscadorMiEd= new Ext.ux.grid.Search({
        iconCls:'icon-zoom',
    //    readonlyIndexes:['id_convocatoria'],
        disableIndexes:['supervisor','estado','id_evaluacion','semestre','anio','a_fm','a_pm','a_fyam','a_c2','a_c1'],
        align:'left',
        minChars:3
    });
  
   misEeddListingEditorGrid =  new Ext.grid.GridPanel({
        id: 'misEeddListingEditorGrid',
        title: 'Listado de Autoevaluaciones y Evaluaciones de Desempeño! ',
        store: misEeddDataStore,
        cm: misEeddColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        view: new Ext.grid.GroupingView({
            forceFit:false,
            headersDisabled :true,
            groupTextTpl: '{group} ({[values.rs.length]} {[values.rs.length > 1 ? "evaluaciones" : "evaluación"]})'
        }),
        plugins:[buscadorMiEd],
        clicksToEdit:2,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador
        /*tbar: [
          {
            text: 'Nuevo ...',
            tooltip: 'Crear un nuevo...',
            iconCls:'add',                      // reference to our css
//            handler: ,
            hidden: !permiso_alta
          }, '-', { 
            text: 'Eliminar',
            tooltip: 'Eliminar ...',
            handler: ,   // Confirm before deleting
            iconCls:'remove',
			hidden: !permiso_eliminar
          }
      ]*/
    });   

  misEeddDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
  
  function showEstado (value,metaData,superData){
    var estado=superData.json.id_estado;
    switch (estado)
    {
        case '1':
        metaData.attr = 'style="background-color:#FF8000; color:#FFF;"';
        break;
        case '2':
        metaData.attr = 'style="background-color:#FFE500; color:#8B8B8A;"';
        break;
        case '3':
        metaData.attr = 'style="background-color:#688A08; color:#FFF;"';
        break;
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}
  function showCumplimiento (value,metaData,superData){
    var tipo=superData.json.tipo;
    var cierre_s=superData.json.cierre_s;
    if(tipo==1 && cierre_s==0)
        value="-";
    metaData.attr = 'style="background-color:#000; color:#FFF;"';
    return value;
}
  function showAvance (value,metaData,record,a,b,c,col){
    var tipo=record.json.tipo;
    switch (tipo)
    {
        case '1':
            if (col =='c1')
            {
                if (value==1)
                {
                    metaData.attr = 'style="background-color:#04B404; color:#FFF;"';
                    metaData.attr += 'ext:qtip="Completo"';
                    value='&checkmark;';
                }
                else
                {
                    metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
                    metaData.attr += 'ext:qtip="Incompleto-Obligatorio"';
                    value='x';
                } 
            }
            else
            {
                metaData.attr = 'style="background-color:#A4A4A4; color:#A4A4A4;"';
                value='';
            }
        break;
        case '2':
            if (value==1)
            {
                metaData.attr = 'style="background-color:#04B404; color:#FFF;"';
                metaData.attr += 'ext:qtip="Completo"';
                value='&checkmark;';
            }
            else
            {
                metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
                metaData.attr += 'ext:qtip="Incompleto-Obligatorio"';
                value='x';
            }
        break;
    }
        
    return value;
}
  function showAvanceOptativo (value,metaData,record,a,b,c,col){
    var tipo=record.json.tipo;
    switch (tipo)
    {
        case '1':
            if (col =='c1')
            {
                if (value==1)
                {
                    metaData.attr = 'style="background-color:#04B404; color:#FFF;"';
                    metaData.attr += 'ext:qtip="Completo"';
                    value='&checkmark;';
                }
                else
                {
                    metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
                    metaData.attr += 'ext:qtip="Incompleto-Opcional"';
                    value='x';
                } 
            }
            else
            {
                metaData.attr = 'style="background-color:#A4A4A4; color:#A4A4A4;"';
                value='';
            }
        break;
        case '2':
            if (value==1)
            {
                metaData.attr = 'style="background-color:#04B404; color:#FFF;"';
                metaData.attr += 'ext:qtip="Completo"';
                value='&checkmark;';
            }
            else
            {
                metaData.attr = 'style="background-color:#FF8000; color:#FFF;"';
                metaData.attr += 'ext:qtip="Incompleto-Opcional"';
                value='x';
            }
        break;
    }
        
    return value;
}
  function showTipo (value,metaData,superData){
    var tipo=superData.json.tipo;
    switch (tipo)
    {
        case '1':
        metaData.attr = 'style="background-color:#00B0F0; color:#FFF;"';
        value='Mis autoevaluaciones';
        break;
        case '2':
        metaData.attr = 'style="background-color:#FFFF00; color:#FFF;"';
        value='Mis evaluados';
        break;
    }
    return value;
}
   
var altura=Ext.getBody().getSize().height - 60;
misEeddListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
        misEeddListingEditorGrid.setWidth(this.getSize().width);
        misEeddListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

});

function showBtnEditarMiED(value,metaData,record){
    var a=record.json.tipo;
    var u=record.json.cierre_u;
    if(a==1 && u==0)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showBtnSupervisarED(value,metaData,record){
    var a=record.json.tipo;
    var s=record.json.cierre_s;
    if(a==2 && s==0)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showBtnCerrar(value,metaData,record,x,y,z,op){
    var t=record.json.tipo;
    var b=record.json.btn_cerrar;
    var u=record.json.cierre_u;
    var s=record.json.cierre_s;
    switch (op)
    {
        case 'btn_si':
            if(b==1)
                return 'x-grid-center-icon';                
            else
                return 'x-hide-display';  
        break;
        
        case 'btn_no':
            if(b==1)
                return 'x-hide-display';  
            else
            {
                if(t==1 && u==1)
                    return 'x-hide-display';  
                else if(t==2 && s==1)
                    return 'x-hide-display';  
                else
                    return 'x-grid-center-icon';                
            }
        break;
    }
};
function clickBtnEditarMiED(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id_evaluacion;
    Ext.get('browser').load({
        url: CARPETA_MI_ED+"/index",
        params: {id: id},
        scripts: true,
        text: "Cargando..."
    });
}
function clickBtnSupervisarED(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id_evaluacion;
    Ext.get('browser').load({
        url: CARPETA_SU_ED+"/index",
        params: {id: id},
        scripts: true,
        text: "Cargando..."
    });
}
function clickBtnVerEDPdf(grid,rowIndex,colIndex,item ,event){
    var record=grid.getStore().getAt(rowIndex);
    var id=record.json.id_evaluacion;
    var semestre=record.json.semestre;
    var anio=record.json.anio;
    var nom="ED-"+anio+semestre+"-"+1000000+id;
    window.open(CARPETA_PDF+'/ver_ed/'+id+"/"+nom)
  };
  
function clickBtnCerrarED_no(grid,rowIndex,colIndex,item ,event){
    var record=grid.getStore().getAt(rowIndex);
    var t=record.json.tipo;
    var ac1=record.json.a_c1;
    var u=record.json.cierre_u;
    var s=record.json.cierre_s;
    if (t==1 && s==0 && ac1==1)
        Ext.MessageBox.alert('Error','Debe Aguardar hasta que su supervisor cierre primero la evalluación');
    else    
        Ext.MessageBox.alert('Error','Debe completar todos los pasos en la evaluación para poder cerrarla');
}
function clickBtnCerrarED(grid,rowIndex,colIndex,item ,event){
    sesionControl();
    var txt;
    var tipo=grid.getStore().getAt(rowIndex).json.tipo;// 1->Autoevaluación 2->Supervisando evaluacion
    switch (tipo)
    {
        case '1': //Autoevaluacion
        case 1:
             var cierre_s=grid.getStore().getAt(rowIndex).json.cierre_s;
             if(cierre_s==1)
             {
                txt='¿Confirma que desea cerrar su autoevaluación?';
                clickBtnCerrarEdConObs (grid,rowIndex,colIndex,item,event,tipo);
             }
             else
             {
                 Ext.MessageBox.alert('Error','La evaluación debe estar cerrada primero pos su supervisor');
             }
        break;
        case '2'://Supervisando evaluacion
        case 2://Supervisando evaluacion
             cerrarEdSup(grid,rowIndex,colIndex,item,event,tipo);
         break;

    }
}
function cerrarEdSup(grid,rowIndex,colIndex,item,event,tipo){
        var usuario=grid.getStore().getAt(rowIndex).json.usuario;// 1->Autoevaluación 2->Supervisando evaluacion
        var periodo=grid.getStore().getAt(rowIndex).json.periodo;// 1->Autoevaluación 2->Supervisando evaluacion
        txt='¿Confirma que desea cerrar la Evaluación de Desempeño <b>'+periodo+' </b>del usuario <b>'+usuario+'</b>?';
        Ext.MessageBox.confirm('Confirme',txt,function (btn, text){
        if(btn=='yes'){
           msgProcess('Cerrando evalución...'); 
           Ext.Ajax.request({   
              waitMsg: 'Por favor espere...',
              url: CARPETA+'/cerrar',
              params: {
                         id        : grid.getStore().getAt(rowIndex).json.id_evaluacion,     
                         tipo      : tipo,// 1->Autoevaluación 2->Supervisando evaluacion
                         usuario   : grid.getStore().getAt(rowIndex).json.usuario
              }, 
              success: function(response){              
                 var result=eval(response.responseText);
                 switch(result.success){
                 case true:
                    Ext.MessageBox.alert('OK',result.msg);
                    misEeddDataStore.commitChanges();
                    misEeddDataStore.reload();
                    break;
                 case false:
                    Ext.MessageBox.alert('Error',result.error);
//                  misEeddDataStore.commitChanges();
//                  misEeddDataStore.reload();
                    break;
                 default:
                    Ext.MessageBox.alert('Error','Error inesperado, por favor informe al área de IT');
                    break;
                 }
              },
              failure: function(response){
                 var result=response.responseText;
                 Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
              }                      
           });   
        }
    });
}
 function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}