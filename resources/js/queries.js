import {mutate} from "swrv";

function prefetchRules(account) {
    const rulesPath = route('api.pm.client-account.rules', [account]);
    mutate(
        rulesPath,
        axios(rulesPath).then((res) => res.data)
    )
}

function prefetchTaxonomy(account) {
    const taxonomyPath = route('api.pm.client-account.taxonomy', [account]);
    mutate(
        taxonomyPath,
        axios(taxonomyPath).then((res) => res.data)
    )
}


export {prefetchTaxonomy, prefetchRules}
