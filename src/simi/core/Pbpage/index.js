import React from 'react'
import Pbpage from './Pbpage'
import { Route } from 'src/drivers';

export const PbPageHoc = props => {
    return (
        <Route
            render={({ history, location }) => {
                    const pbProps = {
                        ...{history: history, location: location},
                        ...props
                    }
                    return (<Pbpage {...pbProps}/>)
                }
            }
        />
    )
}