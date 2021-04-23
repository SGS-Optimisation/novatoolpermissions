    var Ziggy = {
        namedRoutes: {"debugbar.openhandler":{"uri":"_debugbar\/open","methods":["GET","HEAD"],"domain":null},"debugbar.clockwork":{"uri":"_debugbar\/clockwork\/{id}","methods":["GET","HEAD"],"domain":null},"debugbar.telescope":{"uri":"_debugbar\/telescope\/{id}","methods":["GET","HEAD"],"domain":null},"debugbar.assets.css":{"uri":"_debugbar\/assets\/stylesheets","methods":["GET","HEAD"],"domain":null},"debugbar.assets.js":{"uri":"_debugbar\/assets\/javascript","methods":["GET","HEAD"],"domain":null},"debugbar.cache.delete":{"uri":"_debugbar\/cache\/{key}\/{tags?}","methods":["DELETE"],"domain":null},"nova-settings.get":{"uri":"nova-vendor\/nova-settings\/settings","methods":["GET","HEAD"],"domain":null},"nova-settings.save":{"uri":"nova-vendor\/nova-settings\/settings","methods":["POST"],"domain":null},"larecipe.search":{"uri":"docs\/search-index\/{version}","methods":["GET","HEAD"],"domain":null},"larecipe.styles":{"uri":"docs\/styles\/{style}","methods":["GET","HEAD"],"domain":null},"larecipe.scripts":{"uri":"docs\/scripts\/{script}","methods":["GET","HEAD"],"domain":null},"larecipe.index":{"uri":"docs","methods":["GET","HEAD"],"domain":null},"larecipe.show":{"uri":"docs\/{version}\/{page?}","methods":["GET","HEAD"],"domain":null},"login":{"uri":"login","methods":["GET","HEAD"],"domain":null},"logout":{"uri":"logout","methods":["POST"],"domain":null},"user-profile-information.update":{"uri":"user\/profile-information","methods":["PUT"],"domain":null},"password.confirm":{"uri":"user\/confirm-password","methods":["GET","HEAD"],"domain":null},"password.confirmation":{"uri":"user\/confirmed-password-status","methods":["GET","HEAD"],"domain":null},"profile.show":{"uri":"user\/profile","methods":["GET","HEAD"],"domain":null},"other-browser-sessions.destroy":{"uri":"user\/other-browser-sessions","methods":["DELETE"],"domain":null},"current-user.destroy":{"uri":"user","methods":["DELETE"],"domain":null},"current-user-photo.destroy":{"uri":"user\/profile-photo","methods":["DELETE"],"domain":null},"teams.create":{"uri":"teams\/create","methods":["GET","HEAD"],"domain":null},"teams.store":{"uri":"teams","methods":["POST"],"domain":null},"teams.show":{"uri":"teams\/{team}","methods":["GET","HEAD"],"domain":null},"teams.update":{"uri":"teams\/{team}","methods":["PUT"],"domain":null},"teams.destroy":{"uri":"teams\/{team}","methods":["DELETE"],"domain":null},"current-team.update":{"uri":"current-team","methods":["PUT"],"domain":null},"team-members.store":{"uri":"teams\/{team}\/members","methods":["POST"],"domain":null},"team-members.update":{"uri":"teams\/{team}\/members\/{user}","methods":["PUT"],"domain":null},"team-members.destroy":{"uri":"teams\/{team}\/members\/{user}","methods":["DELETE"],"domain":null},"telescope":{"uri":"telescope\/{view?}","methods":["GET","HEAD"],"domain":null},"nova.login":{"uri":"nova\/login","methods":["GET","HEAD"],"domain":null},"nova.logout":{"uri":"nova\/logout","methods":["GET","POST","HEAD"],"domain":null},"nova.password.request":{"uri":"admin\/password\/reset","methods":["GET","HEAD"],"domain":null},"nova.password.email":{"uri":"admin\/password\/email","methods":["POST"],"domain":null},"nova.password.reset":{"uri":"admin\/password\/reset\/{token}","methods":["GET","HEAD"],"domain":null},"rule_search":{"uri":"api\/rule\/search\/{jobNumber}","methods":["GET","HEAD"],"domain":null},"home":{"uri":"\/","methods":["GET","HEAD"],"domain":null},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"],"domain":null},"job.rules":{"uri":"{jobNumber?}","methods":["GET","HEAD"],"domain":null},"job.search":{"uri":"{jobNumber?}","methods":["POST"],"domain":null},"rule.flag":{"uri":"rule\/{rule}\/flag","methods":["POST"],"domain":null},"rule.unflag":{"uri":"rule\/{rule}\/unflag","methods":["POST"],"domain":null},"pm.landing":{"uri":"pm","methods":["GET","HEAD"],"domain":null},"pm.client-account.create":{"uri":"pm\/client-account\/create","methods":["GET","HEAD"],"domain":null},"pm.client-account.store":{"uri":"pm\/client-account","methods":["POST"],"domain":null},"pm.client-account.getById":{"uri":"pm\/{id}","methods":["GET","HEAD"],"domain":null},"pm.client-account.":{"uri":"pm\/{clientAccount}","methods":["GET","HEAD"],"domain":null},"pm.client-account.dashboard":{"uri":"pm\/{clientAccount}\/dashboard","methods":["GET","HEAD"],"domain":null},"pm.client-account.edit":{"uri":"pm\/{clientAccount}\/edit","methods":["GET","HEAD"],"domain":null},"pm.client-account.update":{"uri":"pm\/{clientAccount}\/update","methods":["POST"],"domain":null},"pm.client-account.taxonomy":{"uri":"pm\/{clientAccount}\/taxonomy","methods":["GET","HEAD"],"domain":null},"pm.client-account.rules":{"uri":"pm\/{clientAccount}\/rules","methods":["GET","HEAD"],"domain":null},"pm.client-account.rules.create":{"uri":"pm\/{clientAccount}\/rules\/create","methods":["GET","HEAD"],"domain":null},"pm.client-account.rules.store":{"uri":"pm\/{clientAccount}\/rules\/store","methods":["POST"],"domain":null},"pm.client-account.rules.edit":{"uri":"pm\/{clientAccount}\/rules\/{id}\/edit","methods":["GET","HEAD"],"domain":null},"pm.client-account.rules.history":{"uri":"pm\/{clientAccount}\/rules\/{id}\/history","methods":["GET","HEAD"],"domain":null},"pm.client-account.rules.update":{"uri":"pm\/{clientAccount}\/rules\/{id}\/update","methods":["PUT"],"domain":null},"pm.client-account.rules.taxonomy.update":{"uri":"pm\/{clientAccount}\/rules\/{id}\/taxonomy\/update","methods":["PUT"],"domain":null},"pm.taxonomies.store":{"uri":"pm\/taxonomies","methods":["POST"],"domain":null},"pm.taxonomies.update":{"uri":"pm\/taxonomies\/{id}","methods":["PUT"],"domain":null},"pm.taxonomies.destroy":{"uri":"pm\/taxonomies\/{id}","methods":["DELETE"],"domain":null},"pm.terms.store":{"uri":"pm\/terms","methods":["POST"],"domain":null},"pm.terms.destroy":{"uri":"pm\/terms\/{id}","methods":["PUT"],"domain":null},"nova.index":{"uri":"admin","methods":["GET","HEAD"],"domain":null}},
        baseUrl: 'https://dagobah.test/',
        baseProtocol: 'https',
        baseDomain: 'dagobah.test',
        basePort: false,
        defaultParameters: []
    };

    if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
        for (var name in window.Ziggy.namedRoutes) {
            Ziggy.namedRoutes[name] = window.Ziggy.namedRoutes[name];
        }
    }

    export {
        Ziggy
    }
