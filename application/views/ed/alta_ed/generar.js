// JavaScript Document
function clickBtnGenerarEEDD (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnGenerarEEDD(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnGenerarEEDD (grid, rowIndex)
{
    function generarEEDD(){
        if(isModuloFormValid()){
           msgProcess('Generando...');
           Ext.Ajax.request({  
               waitMsg: 'Por favor espere',
               url: CARPETA+'/generar', 
               params: { 
                   tipo          : 1,
//                   semestre      : semestresCombo.getValue(),
                   semestre      : semestresRadios.getValue().inputValue,
                   anio          : aniosCombo.getValue(),
                   empresa       : empresasCombo.getValue()
                 }, 
               success: function(response){
                 Ext.MessageBox.hide(); 
                 var result=eval(response.responseText);
                 switch(result){
                 case 1:  
                 case '1': 
                   altaEEDDDataStore.reload();
                   generarEEDDCreateWindow.close();
                   break;
                 default:
                   altaEEDDDataStore.reload();
                   generarEEDDCreateWindow.close();
                   break;
                 }
               },
               failure: function(response){
                 var result=response.responseText;
                 Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                 }
           });

       } else {
         Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
       }
     }

  
  // check if the form is valid
  function isModuloFormValid(){	  
//        var v1  = semestresCombo.isValid();
        var v1  = semestresRadios.isValid();
        var v2  = aniosCombo.isValid();
        var v3  = empresasCombo.getValue();
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
//    var arraySemestres=['1','2'];  
//    semestresCombo = new Ext.form.ComboBox({
//        id:'semestresCombo',
//        forceSelection : false,
//        fieldLabel: 'Semestre',
//        store: arraySemestres,
//        editable : false,
//        allowBlank: false,
//        blankText:'campo requerido',
//        anchor:'95%',
//        mode:'local',
//        triggerAction: 'all',
//        width: 300
//    });
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
    
    
 empresasDS = new Ext.data.Store({
        id: 'empresasDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/empresas_combo', 
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
        }, [
      	{name: 'id_empresa', type: 'int'},        
        {name: 'empresa', type: 'string'},
      ])
    });
    empresasDS.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id_empresa', type: 'int'},
		{name: 'empresa', type: 'string'}
	);
	var myNewT = new tRecord({
		id_empresa: -1,
		empresa   : 'Todas'
	});
	empresasDS.insert( 0, myNewT);	
} );
    empresasCombo = new Ext.form.ComboBox({
        id:'empresasCombo',
        forceSelection : false,
        fieldLabel: 'Empresa',
        store: empresasDS,
        editable : false,
        displayField: 'empresa',
        disabled:false,
        allowBlank: false,
        blankText:'campo requerido',
        valueField: 'id_empresa',
        anchor:'95%',
        triggerAction: 'all',
        width: 300
    });
    
//anioField = new Ext.form.NumberField({
//        id: 'anioField',
//        fieldLabel: 'Año',
//        allowBlank: false,
//        allowDecimals:false,
//        allowNegative:false,
//        minValue:new Date().getFullYear()-1,
//        minText:'Ingresar desde el año '+new Date().getFullYear()-1,
//        maxValue :new Date().getFullYear()+1,
//        maxText:'Valor máximo '+new Date().getFullYear()+1,
//        blankText:'campo requerido',
//        disabled:false,
//        tabIndex:1,
//        anchor : '95%'
//    });

    GenerarEEDDCreateForm = new Ext.FormPanel({
        id:'altaGenerarEEDD-form',
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
                    items: [semestresRadios,aniosCombo,empresasCombo]
                    }
                ]
        }
        ],
	buttons: [{
            text: 'Generar',
            handler: generarEEDD
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                generarEEDDCreateWindow.close();
            }
            }]
    });
	
 
    generarEEDDCreateWindow= new Ext.Window({
        id: 'generarEEDDCreateWindow',
        title: 'Generar evaluaciones para un nuevo período',
        closable:false,
        modal:true,
        width: 300,
        height: 200,
        plain:true,
        layout: 'fit',
        items: GenerarEEDDCreateForm,
        closeAction: 'close'
    });		
    generarEEDDCreateWindow.show();
    
 }//fin 