<?php

namespace Jsanbae\SIIAPI\Constants;

class RCV
{
    const RCV_REFERER = "https://www4.sii.cl/consdcvinternetui/";
    const RCV_ORIGIN = "https://www4.sii.cl";
    
    const RCV_RESUMEN_ENDPOINT = "https://www4.sii.cl/consdcvinternetui/services/data/facadeService/getResumen";
    const RCV_RESUMEN_NAMESPACE = "cl.sii.sdi.lob.diii.consdcv.data.api.interfaces.FacadeService/getResumen";
    
    const LIBRO_COMPRAS_DETALLE_NAMESPACE = "cl.sii.sdi.lob.diii.consdcv.data.api.interfaces.FacadeService/getDetalleCompra";
    const LIBRO_COMPRAS_DETALLE_ENDPOINT = "https://www4.sii.cl/consdcvinternetui/services/data/facadeService/getDetalleCompra";
    const LIBRO_VENTAS_DETALLE_NAMESPACE = "cl.sii.sdi.lob.diii.consdcv.data.api.interfaces.FacadeService/getDetalleVenta";
    const LIBRO_VENTAS_DETALLE_ENDPOINT = "https://www4.sii.cl/consdcvinternetui/services/data/facadeService/getDetalleVenta";
}
