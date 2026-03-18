from flask import Blueprint, jsonify
from flask_jwt_extended import jwt_required

from app.services import BidService


bids_bp = Blueprint("bids", __name__, url_prefix="/api/bids")


@bids_bp.get("")
@jwt_required()
def list_bids() -> tuple[list[dict], int]:
    bids = BidService.list_active_bids()
    return jsonify([
        {"id": bid.id, "bidding_number": bid.bidding_number, "bidding_modality": bid.bidding_modality}
        for bid in bids
    ]), 200
