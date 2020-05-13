var arbolDropBoxPanel = new Ext.tree.TreePanel({
    	id: 'arbolDropBoxPanel',
    	title: 'Directorio',
        region:'west',
        split: true,
        height: 300,
        minSize: 150,
        iconCls: 'dropbox_ico',
        width:350,
        autoScroll: true,
        // tree-specific configs:
        rootVisible: false,
        lines: false,
        singleExpand: true,
        useArrows: true,
        
        loader: new Ext.tree.TreeLoader({
            dataUrl:CARPETA+'/arbol'
        }),
        
        root: new Ext.tree.AsyncTreeNode()
    });
    
    arbolDropBoxPanel.on('click', function(n){
        if (n.leaf) // ignoro click en directorio raiz
        {
            var id=n.id;
            archivosDropBoxDataStore.load({params: {id:id,start: 0}});
        }
    });