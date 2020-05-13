// JavaScript Document
function clickBtnAltaUnica (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnAltaUnica(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnAltaUnica (grid, rowIndex)
{
    function generarED(){
        if(isModuloFormValid()){
           msgProcess('Generando...');
           Ext.Ajax.request({  
               waitMsg: 'Por favor espere',
               url: CARPETA+'/generar', 
               params: { 
                   tipo          : 2,
                   semestre      : semestresRadios.getValue().inputValue,
                   anio          : aniosCombo.getValue(),
                   usuario       : usuarioCombo.getValue()
                 }, 
               success: function(response){
                    Ext.MessageBox.hide(); 
                    var result=eval(response.responseText);
                    if(result.success){
                        Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                        generarEDCreateWindow.close();
                        altaEEDDDataStore.reload();
                   } 
                   else
                        Ext.MessageBox.alert('Error',result.msg);
               },
               failure: function(response){
                 var result=eval(response.responseText);
                 Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                 }
           });

       } else {
         Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
       }
     }

  
  // check if the form is valid
  function isModuloFormValid(){	  
        var v1  = semestresRadios.isValid();
        var v2  = aniosCombo.isValid();
        var v3  = usuarioCombo.getValue();
      return( v1 && v2 && v3);
  }
   
	  
  // reset the Form before opening it
    function resetPresidentForm(){
        anioField.setValue('');
        numeroField.setValue('');
  }	
  var semestresRadios = new Ext.form.RadioGroup({ 
    id:'semestresRadios',
    fieldLabel: 'Semestre',
    anchor:'95%',
    tabIndex:3,
    columns: 2,
    items: [ 
          {boxLabel: '1°', name: 'semestre', inputValue: '1', checked: true}, 
          {boxLabel: '2°', name: 'semestre', inputValue: '2'}
     ] 
});
    var arrayAnios=[new Date().getFullYear()-1,new Date().getFullYear(),new Date().getFullYear()+1];  
    aniosCombo = new Ext.form.ComboBox({
        id:'aniosCombo',
        forceSelection : false,
        fieldLabel: 'Año',
        store: arrayAnios,
        editable : false,
        allowBlank: false,
        blankText:'campo requerido',
        anchor:'95%',
        mode:'local',
        triggerAction: 'all',
        width: 300
    });
    
    
 usuarioDS = new Ext.data.Store({
        id: 'usuarioDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_usuarios', 
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
        }, [
      	{name: 'id_usuario', type: 'int'},        
        {name: 'nomape', type: 'string'},
        {name: 'puesto', mapping: 'puesto'}
      ])
    });
    var usuarioAltaED_Tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    usuarioCombo = new Ext.form.ComboBox({
        id:'usuarioCombo',
        forceSelection : true,
        typeAhead: false,
        loadingText: 'Buscando...',
        fieldLabel: 'Usuario',
        store: usuarioDS,
        editable : true,
        displayField: 'nomape',
        disabled:false,
        allowBlank: false,
        blankText:'campo requerido',
        valueField: 'id_usuario',
        anchor:'95%',
//        triggerAction: 'all',
        width: 300,
        tpl: usuarioAltaED_Tpl,
        itemSelector: 'div.search-item',
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
    });
    
    AltaUnicaCreateForm = new Ext.FormPanel({
        id:'altaAltaUnica-form',
        labelAlign: 'left',
        labelWidth:80,
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [semestresRadios,aniosCombo,usuarioCombo]
                    }
                ]
        }
        ],
	buttons: [{
            text: 'Generar',
            handler: generarED
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                generarEDCreateWindow.close();
            }
            }]
    });
	
 
    generarEDCreateWindow= new Ext.Window({
        id: 'generarEDCreateWindow',
        title: 'Generar evaluaciones para un nuevo período',
        closable:false,
        modal:true,
        width: 400,
        height: 200,
        plain:true,
        layout: 'fit',
        items: AltaUnicaCreateForm,
        closeAction: 'close'
    });		
    generarEDCreateWindow.show();
    
 }//fin 