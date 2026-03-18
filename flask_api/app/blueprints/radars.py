from flask import Blueprint, jsonify, request
from flask_jwt_extended import jwt_required

from app.services import RadarService


radars_bp = Blueprint("radars", __name__, url_prefix="/api/radars")


@radars_bp.get("")
@jwt_required()
def list_radars() -> tuple[list[dict], int]:
    radars = RadarService.list_radars(request.args.to_dict())
    return jsonify([
        {
            "id": radar.id,
            "titulo": radar.titulo,
            "situacao": radar.situacao,
            "state_id": radar.state_id,
            "modality_id": radar.modality_id,
        }
        for radar in radars
    ]), 200
