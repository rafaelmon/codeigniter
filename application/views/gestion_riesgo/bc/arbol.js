function create(id)
{
    console.log('arbol');
    var bcTree = new Ext.ux.tree.TreeGrid({
            id:'bcTree',
            width: 600,
            autoScroll: true,
            height: 300,
            enableDD: false,
            columns:[{
                header: 'Usuarios',
                dataIndex: 'task',
                width: 230
            },{
                header: 'Fecha',
                width: 150,
                dataIndex: 'user'
            }],
            loader: new Ext.tree.TreeLoader({	
                dataUrl: CARPETA+'/arbol',
                baseParams: {
                    id: id
                }
            })
    });
    
//    console.log(bcTree.getRootNode().reload());
}
    
var arbolPanel = new Ext.Panel(
{
        id:'arbolBcPanel',
        collapsible: false,
        collapsed:false,
        split: true,
        title: 'Detalle del proceso',
        region: 'east',
        height: 300,
        width: 400,
        minSize: 400,
        maxSize: 600,
        margins: '0 5 5 5',
//        html:'<p>panel superior</p>',
        layout: 'fit'
//        items : []
});