from __future__ import annotations

from typing import Any

from flask import Blueprint, jsonify, request
from flask_jwt_extended import jwt_required

from app.services import BidService


bids_bp = Blueprint("bids", __name__, url_prefix="/api/bids")


def serialize_bid(bid: Any) -> dict[str, Any]:
    return {
        "id": bid.id,
        "bidding_number": bid.bidding_number,
        "bidding_modality": bid.bidding_modality,
        "requesting_agency": bid.requesting_agency,
        "registration_email": bid.registration_email,
        "auctioneer_name": bid.auctioneer_name,
    }


@bids_bp.get("", endpoint="list_bids")
@jwt_required(optional=True)
def list_bids_view() -> tuple[Any, int]:
    bids = BidService.list_active_bids()
    return jsonify([serialize_bid(bid) for bid in bids]), 200


@bids_bp.post("", endpoint="create_bid")
@jwt_required(optional=True)
def create_bid_view() -> tuple[dict[str, Any], int]:
    payload = request.get_json(force=True)
    required = ["bidding_modality", "bidding_number"]
    missing = [field for field in required if not payload.get(field)]
    if missing:
        return {"error": f"Campos obrigatórios ausentes: {', '.join(missing)}"}, 422

    bid = BidService.create_bid(payload)
    return serialize_bid(bid), 201


@bids_bp.put("/<int:bid_id>", endpoint="update_bid")
@jwt_required(optional=True)
def update_bid_view(bid_id: int) -> tuple[dict[str, Any], int]:
    bid = BidService.update_bid(bid_id, request.get_json(force=True))
    return serialize_bid(bid), 200


@bids_bp.delete("/<int:bid_id>", endpoint="delete_bid")
@jwt_required(optional=True)
def delete_bid_view(bid_id: int) -> tuple[dict[str, str], int]:
    BidService.delete_bid(bid_id)
    return {"status": "deleted"}, 200
