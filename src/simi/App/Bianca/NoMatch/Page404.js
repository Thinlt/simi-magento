import React from 'react'
import Identify from 'src/simi/Helper/Identify';
require('./style.scss')

const Page404 = ()=>{
    return (
        <div className="page-404">
            <div className="container-404">
                <p className="title-404">{Identify.__('404')}</p>
                <p className="sub-title-404">{Identify.__('Page not found')}</p>
                <div className="desc-404">
                    {Identify.__('The resource requested could not be found on this server!')}
                </div>
            </div>
        </div>
    )
}

export default Page404