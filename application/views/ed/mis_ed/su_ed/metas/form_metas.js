fechaPlazoField = new Ext.form.DateField({
            id:'plazo',
            name:'plazo',
            allowBlank: false,
            tabIndex: 5,
            fieldLabel:'Fecha plazo',
            allowBlank: false,
            anchor : '10%',
            blankText:'campo requerido',
            editable: true,
            minValue:new Date(),
//            maxValue:,
            format:'d/m/Y'
        });

metasForm = new Ext.FormPanel({
        id:'metasForm',
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
                name: 'id_ed_metas',
                id: 'id_ed_metas',
                value: ID_ED,
                hidden : true,
                hideLabel : true,
                cls:'invisible'
            },{
                xtype:'textarea',
                id:'metas',
                name:'metas',
                fieldLabel:'De acuerdo a las metas de la Cia. y de la Gerencia, señale la(s) meta(s) individual(es) que le corresponde(n) a su supervisado, para el siguiente período',
                allowBlank: false,
                anchor:'95%',
                labelStyle: 'font-size:11px;'   
            },
            {
                xtype:'datefield',
                id:'plazo',
                name:'plazo',
                fieldLabel:'Plazo',
                allowBlank: false,
                anchor:'10%',
                labelStyle: 'font-size:11px;',
                format:'d/m/Y'
            }
        ],
        buttons: 
        [{
            text: 'Agregar',
            handler: function(){
                if(metasForm.getForm().isValid())
                {
                    metasForm.getForm().submit({
                        url: CARPETA+'/insertar_meta',
                        waitMsg: 'Agregando...',
                        success: function(metasForm, o)
                        {
                            console.log(o);
                            switch(o.result.success)
                            {
                                case true:
                                case 1:
                                    metasDataStore.reload();
                                    metasForm.reset();
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