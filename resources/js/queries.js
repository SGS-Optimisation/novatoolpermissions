import {mutate} from "swrv";

function prefetchRules(account) {
    const rulesPath = route('api.library.client-account.rules', [account]);
    mutate(
        rulesPath,
        axios(rulesPath).then((res) => res.data)
    )
}

function prefetchTaxonomy(account) {
    const taxonomyPath = route('api.library.client-account.taxonomy', [account]);
    console.log('prefetching taxo for ' + account);
    mutate(
        taxonomyPath,
        axios(taxonomyPath).then((res) => res.data)
    )
}


export {prefetchTaxonomy, prefetchRules}
