
export const getFormattedDate = (data) => {
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    //let dated = Date.parse(data); 
    // console.log(data);
    const t = data.split(/[- :]/);
    // Apply each element to the Date function
    const d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

    const date = new Date(d);
    const dd = date.getDate();
    /* switch (dd){
        case 1:
            dd = dd + 'st'
            break;
        case 2:
            dd = dd + 'nd'
            break;
            case 3:
            dd = dd + 'rd'
            break;
        default:
            dd = dd + 'th'
            break;
    } */
    const yy = date.getFullYear();
    return dd + ' ' + monthNames[date.getMonth()] + ' ' + yy;
}