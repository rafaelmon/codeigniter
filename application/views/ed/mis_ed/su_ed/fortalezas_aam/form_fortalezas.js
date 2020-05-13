fortalezasForm = new Ext.FormPanel({
        id:'fortalezasForm',
    	bodyStyle: 'padding: 5px;',
         labelAlign: 'top',
    	frame:false,
        border: false,
        height:150,
        defaults: {
    		anchor: '95%',
        	msgTarget: 'side'
    	},
        items: 
        [
        
            {
                xtype: 'textfield',
                name: 'id_ed',
                id: 'id_ed',
                value: ID_ED,
                hidden : true,
                hideLabel : true,
                cls:'invisible'
            },{
                xtype:'textarea',
                id:'fortaleza',
                name:'fortaleza',
                fieldLabel:'De acuerdo a su evaluación, resuma las fortalezas y avances observados en el último período (ingrese y guarde de uno en uno)',
                allowBlank: false,
                anchor:'95%',
                labelStyle: 'font-size:11px;'   
            }
        ],
        buttons: 
        [{
            text: 'Agregar',
            handler: function(){
                if(fortalezasForm.getForm().isValid())
                {
                    fortalezasForm.getForm().submit({
                        url: CARPETA+'/insertar_fortaleza',
                        waitMsg: 'Agregando...',
                        success: function(fortalezasForm, o)
                        {
                            console.log(o);
                            switch(o.result.success)
                            {
                                case true:
                                case 1:
                                    fortalezasDataStore.reload();
                                    fortalezasForm.reset();
                                    Ext.MessageBox.alert('OK', o.result.msg);
                                    break;
                                case false:
                                case 0:
                                    Ext.MessageBox.alert('Error', o.result.msg);
                                    break;
                                default:
                                    Ext.MessageBox.alert('Error','Error esperando confirmación...');
                                    break;
                            }
                        },
                        failure: function(){
                            Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');
                        }
                    });
                }
            }
        }
    ]
    });